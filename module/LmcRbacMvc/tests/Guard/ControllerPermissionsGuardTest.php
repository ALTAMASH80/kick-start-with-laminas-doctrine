<?php
/*
 * THIS SOFTWARE IS PROVIDED BY THE COPYRIGHT HOLDERS AND CONTRIBUTORS
 * "AS IS" AND ANY EXPRESS OR IMPLIED WARRANTIES, INCLUDING, BUT NOT
 * LIMITED TO, THE IMPLIED WARRANTIES OF MERCHANTABILITY AND FITNESS FOR
 * A PARTICULAR PURPOSE ARE DISCLAIMED. IN NO EVENT SHALL THE COPYRIGHT
 * OWNER OR CONTRIBUTORS BE LIABLE FOR ANY DIRECT, INDIRECT, INCIDENTAL,
 * SPECIAL, EXEMPLARY, OR CONSEQUENTIAL DAMAGES (INCLUDING, BUT NOT
 * LIMITED TO, PROCUREMENT OF SUBSTITUTE GOODS OR SERVICES; LOSS OF USE,
 * DATA, OR PROFITS; OR BUSINESS INTERRUPTION) HOWEVER CAUSED AND ON ANY
 * THEORY OF LIABILITY, WHETHER IN CONTRACT, STRICT LIABILITY, OR TORT
 * (INCLUDING NEGLIGENCE OR OTHERWISE) ARISING IN ANY WAY OUT OF THE USE
 * OF THIS SOFTWARE, EVEN IF ADVISED OF THE POSSIBILITY OF SUCH DAMAGE.
 *
 * This software consists of voluntary contributions made by many individuals
 * and is licensed under the MIT license.
 */

namespace LmcRbacMvcTest\Guard;

use Laminas\Mvc\MvcEvent;
use Laminas\Mvc\Router\RouteMatch as V2RouteMatch;
use Laminas\Router\RouteMatch;
use LmcRbacMvc\Guard\ControllerGuard;
use LmcRbacMvc\Guard\ControllerPermissionsGuard;
use LmcRbacMvc\Guard\GuardInterface;
use LmcRbacMvc\Role\InMemoryRoleProvider;
use LmcRbacMvc\Service\RoleService;
use LmcRbacMvc\Role\RecursiveRoleIteratorStrategy;

/**
 * @covers \LmcRbacMvc\Guard\AbstractGuard
 * @covers \LmcRbacMvc\Guard\ControllerPermissionsGuard
 */
class ControllerPermissionsGuardTest extends \PHPUnit\Framework\TestCase
{
    private function getMockAuthorizationService()
    {
        $authorizationService = $this->createMock('LmcRbacMvc\Service\AuthorizationService');

        return $authorizationService;
    }

    public function testAttachToRightEvent()
    {
        $guard = new ControllerPermissionsGuard($this->getMockAuthorizationService());

        $eventManager = $this->createMock('Laminas\EventManager\EventManagerInterface');
        $eventManager->expects($this->once())
            ->method('attach')
            ->with(ControllerGuard::EVENT_NAME);

        $guard->attach($eventManager);
    }

    public function rulesConversionProvider()
    {
        return [
            // Without actions
            [
                'rules'    => [
                    [
                        'controller'  => 'MyController',
                        'permissions' => 'post.manage'
                    ],
                    [
                        'controller'  => 'MyController2',
                        'permissions' => ['post.update', 'post.delete']
                    ],
                    new \ArrayIterator([
                        'controller'  => 'MyController3',
                        'permissions' => new \ArrayIterator(['post.manage'])
                    ])
                ],
                'expected' => [
                    'mycontroller'  => [0 => ['post.manage']],
                    'mycontroller2' => [0 => ['post.update', 'post.delete']],
                    'mycontroller3' => [0 => ['post.manage']]
                ]
            ],
            // With one action
            [
                'rules'    => [
                    [
                        'controller'  => 'MyController',
                        'actions'     => 'DELETE',
                        'permissions' => 'permission1'
                    ],
                    [
                        'controller'  => 'MyController2',
                        'actions'     => ['delete'],
                        'permissions' => 'permission2'
                    ],
                    new \ArrayIterator([
                        'controller'  => 'MyController3',
                        'actions'     => new \ArrayIterator(['DELETE']),
                        'permissions' => new \ArrayIterator(['permission3'])
                    ])
                ],
                'expected' => [
                    'mycontroller'  => [
                        'delete' => ['permission1']
                    ],
                    'mycontroller2' => [
                        'delete' => ['permission2']
                    ],
                    'mycontroller3' => [
                        'delete' => ['permission3']
                    ],
                ]
            ],
            // With multiple actions
            [
                'rules'    => [
                    [
                        'controller'  => 'MyController',
                        'actions'     => ['EDIT', 'delete'],
                        'permissions' => 'permission1'
                    ],
                    new \ArrayIterator([
                        'controller'  => 'MyController2',
                        'actions'     => new \ArrayIterator(['edit', 'DELETE']),
                        'permissions' => new \ArrayIterator(['permission2'])
                    ])
                ],
                'expected' => [
                    'mycontroller'  => [
                        'edit'   => ['permission1'],
                        'delete' => ['permission1']
                    ],
                    'mycontroller2' => [
                        'edit'   => ['permission2'],
                        'delete' => ['permission2']
                    ]
                ]
            ],
            // Test that that if a rule is set globally to the controller, it does not override any
            // action specific rule that may have been specified before
            [
                'rules'    => [
                    [
                        'controller'  => 'MyController',
                        'actions'     => ['edit'],
                        'permissions' => 'permission1'
                    ],
                    [
                        'controller'  => 'MyController',
                        'permissions' => 'permission2'
                    ]
                ],
                'expected' => [
                    'mycontroller' => [
                        'edit' => ['permission1'],
                        0      => ['permission2']
                    ]
                ]
            ]
        ];
    }

    /**
     * @dataProvider rulesConversionProvider
     */
    public function testRulesConversions(array $rules, array $expected)
    {
        $controllerGuard = new ControllerPermissionsGuard($this->getMockAuthorizationService(), $rules);

        $reflProperty = new \ReflectionProperty($controllerGuard, 'rules');
        $reflProperty->setAccessible(true);

        $this->assertEquals($expected, $reflProperty->getValue($controllerGuard));
    }

    public function controllerDataProvider()
    {
        return [
            // Test simple guard with both policies
            [
                'rules'               => [
                    [
                        'controller'  => 'BlogController',
                        'permissions' => 'post.edit'
                    ]
                ],
                'controller'          => 'BlogController',
                'action'              => 'edit',
                'identityPermissions' => [['post.edit', null, true]],
                'isGranted'           => true,
                'policy'              => GuardInterface::POLICY_ALLOW
            ],
            [
                'rules'               => [
                    [
                        'controller'  => 'BlogController',
                        'permissions' => 'post.edit'
                    ]
                ],
                'controller'          => 'BlogController',
                'action'              => 'edit',
                'identityPermissions' => [['post.edit', null, true]],
                'isGranted'           => true,
                'policy'              => GuardInterface::POLICY_DENY
            ],
            // Test with multiple rules
            [
                'rules'               => [
                    [
                        'controller'  => 'BlogController',
                        'actions'     => 'read',
                        'permissions' => 'post.read'
                    ],
                    [
                        'controller'  => 'BlogController',
                        'actions'     => 'edit',
                        'permissions' => 'post.edit'
                    ]
                ],
                'controller'          => 'BlogController',
                'action'              => 'edit',
                'identityPermissions' => [
                    ['post.edit', null, true]
                ],
                'isGranted'           => true,
                'policy'              => GuardInterface::POLICY_ALLOW
            ],
            [
                'rules'               => [
                    [
                        'controller'  => 'BlogController',
                        'actions'     => 'read',
                        'permissions' => 'post.read'
                    ],
                    [
                        'controller'  => 'BlogController',
                        'actions'     => 'edit',
                        'permissions' => 'post.edit'
                    ]
                ],
                'controller'          => 'BlogController',
                'action'              => 'edit',
                'identityPermissions' => [
                    ['post.edit', null, true]
                ],
                'isGranted'           => true,
                'policy'              => GuardInterface::POLICY_DENY
            ],
            // Test with multiple permissions. All must be authorized.
            [
                'rules'               => [
                    [
                        'controller'  => 'BlogController',
                        'actions'     => 'admin',
                        'permissions' => ['post.update', 'post.delete'],
                    ],
                ],
                'controller'          => 'BlogController',
                'action'              => 'admin',
                'identityPermissions' => [
                    ['post.update', null, true],
                    ['post.delete', null, true],
                ],
                'isGranted'           => true,
                'policy'              => GuardInterface::POLICY_DENY
            ],
            [
                'rules'               => [
                    [
                        'controller'  => 'BlogController',
                        'actions'     => 'admin',
                        'permissions' => ['post.update', 'post.delete'],
                    ],
                ],
                'controller'          => 'BlogController',
                'action'              => 'admin',
                'identityPermissions' => [
                    ['post.update', null, false],
                    ['post.delete', null, true],
                ],
                'isGranted'           => false,
                'policy'              => GuardInterface::POLICY_DENY
            ],
            [
                'rules'               => [
                    [
                        'controller'  => 'BlogController',
                        'actions'     => 'admin',
                        'permissions' => ['post.update', 'post.delete'],
                    ],
                ],
                'controller'          => 'BlogController',
                'action'              => 'admin',
                'identityPermissions' => [
                    ['post.update', null, true],
                    ['post.delete', null, false],
                ],
                'isGranted'           => false,
                'policy'              => GuardInterface::POLICY_DENY
            ],
            // Assert that policy can deny unspecified rules
            [
                'rules'               => [
                    [
                        'controller'  => 'BlogController',
                        'permissions' => 'post.edit'
                    ],
                ],
                'controller'          => 'CommentController',
                'action'              => 'edit',
                'identityPermissions' => [
                    ['post.edit', null, true]
                ],
                'isGranted'           => true,
                'policy'              => GuardInterface::POLICY_ALLOW
            ],
            [
                'rules'               => [
                    [
                        'controller'  => 'BlogController',
                        'permissions' => 'post.edit'
                    ],
                ],
                'controller'          => 'CommentController',
                'action'              => 'edit',
                'identityPermissions' => [
                    ['post.edit', null, true]
                ],
                'isGranted'           => false,
                'policy'              => GuardInterface::POLICY_DENY
            ],
            // Test assert policy can deny other actions from controller when only one is specified
            [
                'rules'               => [
                    [
                        'controller'  => 'BlogController',
                        'actions'     => 'edit',
                        'permissions' => 'post.edit'
                    ],
                ],
                'controller'          => 'BlogController',
                'action'              => 'read',
                'identityPermissions' => [
                    ['post.edit', null, true]
                ],
                'isGranted'           => true,
                'policy'              => GuardInterface::POLICY_ALLOW
            ],
            [
                'rules'               => [
                    [
                        'controller'  => 'BlogController',
                        'actions'     => 'edit',
                        'permissions' => 'post.edit'
                    ],
                ],
                'controller'          => 'BlogController',
                'action'              => 'read',
                'identityPermissions' => [
                    ['post.edit', null, true]
                ],
                'isGranted'           => false,
                'policy'              => GuardInterface::POLICY_DENY
            ],
            // Assert wildcard in permissions
            [
                'rules'               => [
                    [
                        'controller'  => 'IndexController',
                        'permissions' => '*'
                    ]
                ],
                'controller'          => 'IndexController',
                'action'              => 'index',
                'identityPermissions' => [['post.edit', null, false]],
                'isGranted'           => true,
                'policy'              => GuardInterface::POLICY_ALLOW
            ],
            [
                'rules'               => [
                    [
                        'controller'  => 'IndexController',
                        'permissions' => '*'
                    ]
                ],
                'controller'          => 'IndexController',
                'action'              => 'index',
                'identityPermissions' => [['post.edit', null, false]],
                'isGranted'           => true,
                'policy'              => GuardInterface::POLICY_DENY
            ],
        ];
    }

    /**
     * @dataProvider controllerDataProvider
     */
    public function testControllerGranted(
        array $rules,
        $controller,
        $action,
        $identityPermissions,
        $isGranted,
        $protectionPolicy
    ) {
        $routeMatch = $this->createRouteMatch([
            'controller' => $controller,
            'action' => $action,
        ]);

        $authorizationService = $this->getMockAuthorizationService();
        $authorizationService->expects($this->any())
            ->method('isGranted')
            ->will($this->returnValueMap($identityPermissions));

        $controllerGuard = new ControllerPermissionsGuard($authorizationService, $rules);
        $controllerGuard->setProtectionPolicy($protectionPolicy);

        $event = new MvcEvent();
        $event->setRouteMatch($routeMatch);

        $this->assertEquals($isGranted, $controllerGuard->isGranted($event));
    }

    public function testProperlyFillEventOnAuthorization()
    {
        $event      = new MvcEvent();
        $routeMatch = $this->createRouteMatch([
            'controller' => 'MyController',
            'action' => 'edit',
        ]);

        $application = $this->createMock('Laminas\Mvc\Application');
        $eventManager = $this->createMock('Laminas\EventManager\EventManagerInterface');

        $application->expects($this->never())
            ->method('getEventManager')
            ->will($this->returnValue($eventManager));

        $event->setRouteMatch($routeMatch);
        $event->setApplication($application);

        $identity = $this->createMock('LmcRbacMvc\Identity\IdentityInterface');
        $identity->expects($this->any())->method('getRoles')->will($this->returnValue(['member']));

        $identityProvider = $this->createMock('LmcRbacMvc\Identity\IdentityProviderInterface');
        $identityProvider->expects($this->any())
            ->method('getIdentity')
            ->will($this->returnValue($identity));

        $roleProvider = new InMemoryRoleProvider([
            'member'
        ]);

        $roleService = new RoleService($identityProvider, $roleProvider, new RecursiveRoleIteratorStrategy());

        $routeGuard = new ControllerGuard($roleService, [
            [
                'controller' => 'MyController',
                'actions'    => 'edit',
                'roles'      => 'member'
            ]
        ]);

        $routeGuard->onResult($event);

        $this->assertEmpty($event->getError());
        $this->assertNull($event->getParam('exception'));
    }

    public function testProperlySetUnauthorizedAndTriggerEventOnUnauthorization()
    {
        $event      = new MvcEvent();
        $routeMatch = $this->createRouteMatch([
            'controller' => 'MyController',
            'action' => 'delete',
        ]);

        $application  = $this->createMock('Laminas\Mvc\Application');
        $eventManager = $this->createMock('Laminas\EventManager\EventManager');

        $application->expects($this->once())
            ->method('getEventManager')
            ->will($this->returnValue($eventManager));

        $eventManager->expects($this->once())
            ->method('triggerEvent')
            ->with($event);

        $event->setRouteMatch($routeMatch);
        $event->setApplication($application);

        $identityProvider = $this->createMock('LmcRbacMvc\Identity\IdentityProviderInterface');
        $identityProvider->expects($this->any())
//            ->method('getIdentityRoles')
            ->method('getIdentity')
            ->will($this->returnValue('member'));

        $roleProvider = new InMemoryRoleProvider([
            'member'
        ]);

        $roleService = new RoleService($identityProvider, $roleProvider, new RecursiveRoleIteratorStrategy());

        $routeGuard = new ControllerGuard($roleService, [
            [
                'controller' => 'MyController',
                'actions'    => 'edit',
                'roles'      => 'member'
            ]
        ]);

        $routeGuard->onResult($event);

        $this->assertTrue($event->propagationIsStopped());
        $this->assertEquals(ControllerGuard::GUARD_UNAUTHORIZED, $event->getError());
        $this->assertInstanceOf('LmcRbacMvc\Exception\UnauthorizedException', $event->getParam('exception'));
    }

    public function createRouteMatch(array $params = [])
    {
        $class = class_exists(V2RouteMatch::class) ? V2RouteMatch::class : RouteMatch::class;
        return new $class($params);
    }
}
