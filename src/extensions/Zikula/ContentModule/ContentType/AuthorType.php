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

namespace Zikula\ContentModule\ContentType;

use Zikula\ContentModule\ContentType\Form\Type\AuthorType as FormType;
use Zikula\ExtensionsModule\ModuleInterface\Content\AbstractContentType;
use Zikula\ExtensionsModule\ModuleInterface\Content\ContentTypeInterface;
use Zikula\UsersModule\Api\ApiInterface\CurrentUserApiInterface;
use Zikula\UsersModule\Entity\RepositoryInterface\UserRepositoryInterface;
use Zikula\UsersModule\Entity\UserEntity;

/**
 * Author content type.
 */
class AuthorType extends AbstractContentType
{
    /**
     * @var CurrentUserApiInterface
     */
    protected $currentUserApi;

    /**
     * @var UserRepositoryInterface
     */
    protected $userRepository;

    public function getIcon(): string
    {
        return 'id-card';
    }

    public function getTitle(): string
    {
        return $this->translator->trans('Author information', [], 'contentTypes');
    }

    public function getDescription(): string
    {
        return $this->translator->trans('Various information about the author of the page.', [], 'contentTypes');
    }

    public function getDefaultData(): array
    {
        $userId = null !== $this->currentUserApi ? $this->currentUserApi->get('uid') : 0;

        $data = [
            'author' => $userId,
            'authorName' => '',
            'showAvatar' => true,
            'avatarWidth' => 0,
            'showMessageLink' => true,
        ];

        if (0 < $userId) {
            /** @var UserEntity $user */
            $user = $this->userRepository->find($userId);
            $data['authorName'] = $user->getUname();
        }

        return $data;
    }

    public function getData(): array
    {
        $data = parent::getData();

        /** @var UserEntity $user */
        $user = $this->userRepository->find($data['author']);
        $data['author'] = $user;
        $data['authorName'] = null !== $user ? $user->getUname() : $this->translator->trans('Unknown author', [], 'contentTypes');

        $this->data = $data;

        return $data;
    }

    public function getSearchableText(): string
    {
        /*$data = */$this->getData();

        return html_entity_decode(strip_tags($this->data['authorName'] ?? ''));
    }

    public function getEditFormClass(): string
    {
        return FormType::class;
    }

    public function getAssets(string $context): array
    {
        $assets = parent::getAssets($context);
        if (ContentTypeInterface::CONTEXT_EDIT !== $context) {
            return $assets;
        }

        $assets['css'][] = $this->assetHelper->resolve(
            '@ZikulaUsersModule:css/livesearch.css'
        );
        $assets['js'][] = $this->assetHelper->resolve(
            '@ZikulaUsersModule:js/Zikula.Users.LiveSearch.js'
        );
        $assets['js'][] = $this->assetHelper->resolve(
            '@ZikulaContentModule:js/ZikulaContentModule.ContentType.Author.js'
        );

        return $assets;
    }

    public function getJsEntrypoint(string $context): ?string
    {
        if (ContentTypeInterface::CONTEXT_EDIT !== $context) {
            return null;
        }

        return 'contentInitAuthorEdit';
    }

    /**
     * @required
     */
    public function setAdditionalDepencies(
        CurrentUserApiInterface $currentUserApi,
        UserRepositoryInterface $userRepository
    ): void {
        $this->currentUserApi = $currentUserApi;
        $this->userRepository = $userRepository;
    }
}
