<?php

/**
 * Content.
 *
 * @copyright Axel Guckelsberger (Zikula)
 * @license http://www.gnu.org/licenses/lgpl.html GNU Lesser General Public License
 * @author Axel Guckelsberger <info@ziku.la>.
 *
 * @see https://ziku.la
 *
 * @version Generated by ModuleStudio 1.5.0 (https://modulestudio.de).
 */

declare(strict_types=1);

namespace Zikula\ContentModule\Controller;

use Psr\Log\LoggerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Zikula\ThemeModule\Engine\Annotation\Theme;
use Zikula\UsersModule\Api\ApiInterface\CurrentUserApiInterface;
use Zikula\ContentModule\AppSettings;
use Zikula\ContentModule\Controller\Base\AbstractConfigController;
use Zikula\ContentModule\Helper\PermissionHelper;

/**
 * Config controller implementation class.
 *
 * @Route("/config")
 */
class ConfigController extends AbstractConfigController
{
    /**
     * @Route("/config",
     *        methods = {"GET", "POST"}
     * )
     * @Theme("admin")
     */
    public function config(
        Request $request,
        PermissionHelper $permissionHelper,
        AppSettings $appSettings,
        LoggerInterface $logger,
        CurrentUserApiInterface $currentUserApi
    ): Response {
        return parent::config($request, $permissionHelper, $appSettings, $logger, $currentUserApi);
    }

    // feel free to add your own config controller methods here
}
