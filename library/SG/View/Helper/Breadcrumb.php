<?php

class Zend_View_Helper_Breadcrumb extends Zend_View_Helper_Abstract
{
    public $view;

    public function breadcrumb($params) {
        $breadcrumbs = null;
        $breadcrumbHtml = '';
        switch($params['controller']) {
            case 'user':
                switch($params['action']) {
                    case 'dashboard':
                        $breadcrumbs = array(
                            array(
                                'icon' => 'home',
                                'text' => $this->view->translate("Dashboard"),
                                'url'  => $this->view->routeUrl('cloud-dashboard')
                            ));
                        break;
                    case 'index':
                        $breadcrumbs = array(
                            array(
                                'icon' => 'user',
                                'text' => $this->view->translate("Users"),
                                'url'  => $this->view->routeUrl('cloud-user-index')
                            ));
                        break;
                    case 'create':
                        $breadcrumbs = array(
                            array(
                                'icon' => 'user',
                                'text' => $this->view->translate("Users"),
                                'url'  => $this->view->routeUrl('cloud-user-index')
                            ),
                            array(
                                'text' => $this->view->translate("Create user"),
                                'url'  => $this->view->routeUrl('cloud-user-create')
                            ));
                        break;
                    case 'edit':
                        if (array_key_exists('id', $params) && (int)$params['id'] > 0) {
                            $breadcrumbs = array(
                                array(
                                    'icon' => 'user',
                                    'text' => $this->view->translate("Users"),
                                    'url'  => $this->view->routeUrl('cloud-user-index')
                                ),
                                array(
                                    'text' => $this->view->translate("Edit profile"),
                                    'url'  => $this->view->routeUrl('cloud-user-edit', $params)
                                ));
                        } else {
                            $breadcrumbs = array(
                                array(
                                    'icon' => 'asterisk',
                                    'text' => $this->view->translate("Profile information"),
                                    'url'  => $this->view->routeUrl('cloud-user-edit')
                                ));
                        }
                        break;
                    case 'detail':
                        $breadcrumbs = array(
                            array(
                                'icon' => 'user',
                                'text' => $this->view->translate("Users"),
                                'url'  => $this->view->routeUrl('cloud-user-index')
                            ),
                            array(
                                'text' => $this->view->translate("Detail information"),
                                'url'  => $this->view->routeUrl('cloud-user-detail')
                            ));
                        break;
                    case 'result':
                        $breadcrumbs = array(
                            array(
                                'icon' => 'eye-open',
                                'text' => $this->view->translate("Results"),
                            ));
                        break;
                }
                break;
            case 'form':
                switch($this->view->identity->role) {
                    case Entities\User::TYPE_ADMIN:
                        $breadcrumbs = array(
                            array(
                                'icon' => 'user',
                                'text' => $this->view->translate("Users"),
                                'url'  => $this->view->routeUrl('cloud-user-index')
                            ),
                            array(
                                'text' => $this->view->translate("Detail information"),
                                'url'  => $this->view->routeUrl('cloud-user-detail', array('id' => $this->view->invitation->getUser()->getId()))
                            ),
                            array(
                                'text' => $this->view->translate("Form detail")
                            ));
                        break;

                    case Entities\User::TYPE_USER:
                    default:
                        $breadcrumbs = array(
                            array(
                                'icon' => 'home',
                                'text' => $this->view->translate("Dashboard"),
                                'url'  => $this->view->routeUrl('cloud-dashboard')
                            ),
                            array(
                                'text' => $this->view->invitation->getProject()->getTitle() . ' - ' . $this->view->invitation->getScenario()->getTitle(),
                            ));
                        break;
                }
                break;
            case 'result':
                switch($params['action']) {
                    default:
                        break;
                }
                break;
            case 'project':
                switch($params['action']) {
                    case 'index':
                        $breadcrumbs = array(
                            array(
                                'icon' => 'book',
                                'text' => $this->view->translate("Projects"),
                                'url'  => $this->view->routeUrl('cloud-project-index')
                            ));
                        break;
                    case 'create':
                        $breadcrumbs = array(
                            array(
                                'icon' => 'book',
                                'text' => $this->view->translate("Projects"),
                                'url'  => $this->view->routeUrl('cloud-project-index')
                            ),
                            array(
                                'text' => $this->view->translate("Create project"),
                                'url'  => $this->view->routeUrl('cloud-project-create')
                            ));
                        break;
                    case 'edit':
                        $breadcrumbs = array(
                            array(
                                'icon' => 'book',
                                'text' => $this->view->translate("Projects"),
                                'url'  => $this->view->routeUrl('cloud-project-index')
                            ),
                            array(
                                'text' => $this->view->translate("Edit project"),
                                'url'  => $this->view->routeUrl('cloud-project-edit', $params)
                            ));
                        break;
                    case 'detail':
                        $breadcrumbs = array(
                            array(
                                'icon' => 'book',
                                'text' => $this->view->translate("Projects"),
                                'url'  => $this->view->routeUrl('cloud-project-index')
                            ),
                            array(
                                'text' => $this->view->translate("Project detail"),
                                'url'  => $this->view->routeUrl('cloud-project-detail')
                            ));
                        break;
                    case 'chart-detail':
                        switch($this->view->identity->role) {
                            case Entities\User::TYPE_SUPER_ADMIN:
                            case Entities\User::TYPE_ADMIN:
                                $breadcrumbs = array(
                                    array(
                                        'icon' => 'book',
                                        'text' => $this->view->translate("Projects"),
                                        'url'  => $this->view->routeUrl('cloud-project-index')
                                    ),
                                    array(
                                        'text' => $this->view->translate("Project detail"),
                                        'url'  => $this->view->routeUrl('cloud-project-detail', array('id' => $this->view->project->getId()))
                                    ),
                                    array(
                                        'text' => $this->view->translate("Result detail"),
                                    ));
                                break;
                            case Entities\User::TYPE_USER:
                            default:
                                $breadcrumbs = array(
                                    array(
                                        'icon' => 'eye-open',
                                        'text' => $this->view->translate("Results"),
                                        'url'  => $this->view->routeUrl('cloud-user-result')
                                    ),
                                    array(
                                        'text' => $this->view->translate("Result detail"),
                                    ));
                                break;
                        }
                    break;
                }
                break;
            case 'scenario':
                switch($params['action']) {
                    case 'index':
                        $breadcrumbs = array(
                            array(
                                'icon' => 'list',
                                'text' => $this->view->translate("Scenarios"),
                                'url'  => $this->view->routeUrl('cloud-scenario-index')
                            ));
                        break;
                    case 'create':
                        $breadcrumbs = array(
                            array(
                                'icon' => 'list',
                                'text' => $this->view->translate("Scenarios"),
                                'url'  => $this->view->routeUrl('cloud-scenario-index')
                            ),
                            array(
                                'text' => $this->view->translate("Create scenario"),
                                'url'  => $this->view->routeUrl('cloud-scenario-create')
                            ));
                        break;
                    case 'detail':
                        $breadcrumbs = array(
                            array(
                                'icon' => 'list',
                                'text' => $this->view->translate("Scenarios"),
                                'url'  => $this->view->routeUrl('cloud-scenario-index')
                            ),
                            array(
                                'text' => $this->view->translate("Scenario detail"),
                                'url'  => $this->view->routeUrl('cloud-scenario-detail', $params)
                            ));
                        break;
                    case 'edit':
                        $breadcrumbs = array(
                            array(
                                'icon' => 'list',
                                'text' => $this->view->translate("Scenarios"),
                                'url'  => $this->view->routeUrl('cloud-scenario-index')
                            ),
                            array(
                                'text' => $this->view->translate("Edit scenario information"),
                                'url'  => $this->view->routeUrl('cloud-scenario-edit', $params)
                            ));
                        break;
                    case 'question-create':
                        $breadcrumbs = array(
                            array(
                                'icon' => 'list',
                                'text' => $this->view->translate("Scenarios"),
                                'url'  => $this->view->routeUrl('cloud-scenario-index')
                            ),
                            array(
                                'text' => $this->view->translate("Scenario detail"),
                                'url'  => $this->view->routeUrl('cloud-scenario-detail', $params)
                            ),
                            array(
                                'text' => $this->view->translate("Create question"),
                                'url'  => $this->view->routeUrl('cloud-scenario-question-create', $params)
                            ));
                        break;
                    case 'question-edit':
                        $breadcrumbs = array(
                            array(
                                'icon' => 'list',
                                'text' => $this->view->translate("Scenarios"),
                                'url'  => $this->view->routeUrl('cloud-scenario-index')
                            ),
                            array(
                                'text' => $this->view->translate("Scenario detail"),
                                'url'  => $this->view->routeUrl('cloud-scenario-detail', $params)
                            ),
                            array(
                                'text' => $this->view->translate("Edit question"),
                                'url'  => $this->view->routeUrl('cloud-scenario-question-edit', $params)
                            ));
                        break;
                    case 'section-create':
                        $breadcrumbs = array(
                            array(
                                'icon' => 'list',
                                'text' => $this->view->translate("Scenarios"),
                                'url'  => $this->view->routeUrl('cloud-scenario-index')
                            ),
                            array(
                                'text' => $this->view->translate("Scenario detail"),
                                'url'  => $this->view->routeUrl('cloud-scenario-detail', $params)
                            ),
                            array(
                                'text' => $this->view->translate("Create section"),
                                'url'  => $this->view->routeUrl('cloud-scenario-section-create', $params)
                            ));
                        break;
                    case 'section-edit':
                        $breadcrumbs = array(
                            array(
                                'icon' => 'list',
                                'text' => $this->view->translate("Scenarios"),
                                'url'  => $this->view->routeUrl('cloud-scenario-index')
                            ),
                            array(
                                'text' => $this->view->translate("Scenario detail"),
                                'url'  => $this->view->routeUrl('cloud-scenario-detail', $params)
                            ),
                            array(
                                'text' => $this->view->translate("Edit question"),
                                'url'  => $this->view->routeUrl('cloud-scenario-section-edit', $params)
                            ));
                        break;
                    }
                break;
            case 'assessment':
                switch($params['action']) {
                    case 'index':
                        $breadcrumbs = array(
                            array(
                                'icon' => 'tags',
                                'text' => $this->view->translate("Assessments"),
                                'url'  => $this->view->routeUrl('cloud-assessment-index')
                            ));
                        break;
                    case 'create':
                        $breadcrumbs = array(
                            array(
                                'icon' => 'tags',
                                'text' => $this->view->translate("Assessments"),
                                'url'  => $this->view->routeUrl('cloud-assessment-index')
                            ),
                            array(
                                'text' => $this->view->translate("Create new category"),
                                'url'  => $this->view->routeUrl('cloud-assessment-create')
                            ));
                        break;
                    case 'edit':
                        $breadcrumbs = array(
                            array(
                                'icon' => 'tags',
                                'text' => $this->view->translate("Assessments"),
                                'url'  => $this->view->routeUrl('cloud-assessment-index')
                            ),
                            array(
                                'text' => $this->view->translate("Edit project"),
                                'url'  => $this->view->routeUrl('cloud-assessment-edit', $params)
                            ));
                        break;
                }
                break;
        }

        foreach($breadcrumbs as $index => $breadcrumb) {
            $iconHtml = '';
            if (array_key_exists('icon', $breadcrumb)) {
                $iconHtml = sprintf('<i class="icon-%s"></i>', $breadcrumb['icon']);
            }

            if ($index != sizeof($breadcrumbs) - 1) {
                $breadcrumbHtml .= sprintf('<li><a href="%s">%s %s</a> <span class="divider">/</span></li>',
                    $breadcrumb['url'], $iconHtml, $breadcrumb['text']);
            } else {
                $breadcrumbHtml .= sprintf('<li class="active">%s %s</li>',
                    $iconHtml, $breadcrumb['text']);
            }
        }

        $breadcrumbHtml = '<ul class="breadcrumb well well-small">' . $breadcrumbHtml . '</ul>';
        return $breadcrumbHtml;
    }

    public function setView(Zend_View_Interface $view) {
        $this->view = $view;
    }
}