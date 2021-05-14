<?php

declare(strict_types=1);

namespace AbterPhp\Admin\Events\Listeners;

use AbterPhp\Admin\Constant\Resource;
use AbterPhp\Admin\Constant\Route;
use AbterPhp\Framework\Constant\Html5;
use AbterPhp\Framework\Events\NavigationReady;
use AbterPhp\Framework\Html\Attribute;
use AbterPhp\Framework\Html\Factory\Button as ButtonFactory;
use AbterPhp\Framework\Navigation\Dropdown;
use AbterPhp\Framework\Navigation\Item;
use AbterPhp\Framework\Navigation\Navigation;
use AbterPhp\Framework\Navigation\UserBlock;
use Opulence\Sessions\ISession;

class NavigationBuilder
{
    const FIRST_ITEM_WEIGHT = 0;

    const DEFAULT_BASE_WEIGHT = 1000;

    /** @var ISession */
    protected ISession $session;

    /** @var ButtonFactory */
    protected ButtonFactory $buttonFactory;

    /**
     * NavigationBuilder constructor.
     *
     * @param ISession      $session
     * @param ButtonFactory $buttonFactory
     */
    public function __construct(ISession $session, ButtonFactory $buttonFactory)
    {
        $this->session       = $session;
        $this->buttonFactory = $buttonFactory;
    }

    /**
     * @param NavigationReady $event
     *
     * @throws \Opulence\Routing\Urls\URLException
     */
    public function handle(NavigationReady $event)
    {
        $navigation = $event->getNavigation();

        if (!$navigation->hasIntent(Navigation::INTENT_PRIMARY)) {
            return;
        }

        $this->insertFirstItem($navigation);

        $dropdown   = new Dropdown();
        $dropdown[] = $this->createUserItem();
        $dropdown[] = $this->createUserGroupItem();

        $mainItem = $this->createUserItem();
        $mainItem->setIntent(Item::INTENT_DROPDOWN);
        $mainItem->setAttribute(new Attribute(Html5::ATTR_ID, 'nav-users'));
        $mainItem[0]->setAttribute(new Attribute(Html5::ATTR_HREF, 'javascript:void(0);'));
        $mainItem[1] = $dropdown;

        $logout = $this->createLogoutItem();

        $navigation->addWithWeight(static::DEFAULT_BASE_WEIGHT, $mainItem);
        $navigation->addWithWeight(static::DEFAULT_BASE_WEIGHT, $logout);
    }

    /**
     * @param Navigation $navigation
     *
     * @throws \Opulence\Routing\Urls\UrlException
     */
    protected function insertFirstItem(Navigation $navigation)
    {
        $firstItem = new Item(null, [UserBlock::class]);

        $firstItem[] = $this->createUserBlock();
        $firstItem[] = $this->createUserBlockDropdown();

        $navigation->addWithWeight(static::FIRST_ITEM_WEIGHT, $firstItem);
    }

    /**
     * @return UserBlock
     */
    protected function createUserBlock(): UserBlock
    {
        return new UserBlock($this->session);
    }

    /**
     * @return Dropdown
     * @throws \Opulence\Routing\Urls\UrlException
     */
    protected function createUserBlockDropdown(): Dropdown
    {
        $items   = [];
        $items[] = $this->createProfileItem();
        $items[] = $this->createApiClientsItem();
        $items[] = $this->createLogoutItem();

        return new Dropdown($items);
    }

    /**
     * @return Item
     * @throws \Opulence\Routing\Urls\UrlException
     */
    protected function createApiClientsItem(): Item
    {
        $text = 'admin:apiClients';
        $icon = 'vpn_key';

        $button   = $this->buttonFactory->createFromName($text, Route::API_CLIENTS_LIST, [], $icon);
        $resource = $this->getAdminResource(Resource::API_CLIENTS);

        $item = new Item($button);
        $item->setResource($resource);

        return $item;
    }

    /**
     * @return Item
     * @throws \Opulence\Routing\Urls\UrlException
     */
    protected function createProfileItem(): Item
    {
        $text = 'admin:profile';
        $icon = 'account_box';

        $button = $this->buttonFactory->createFromName($text, Route::PROFILE_EDIT, [], $icon);

        return new Item($button);
    }

    /**
     * @return Item
     * @throws \Opulence\Routing\Urls\UrlException
     */
    protected function createUserItem(): Item
    {
        $text = 'admin:users';
        $icon = 'person';

        $button   = $this->buttonFactory->createFromName($text, Route::USERS_LIST, [], $icon);
        $resource = $this->getAdminResource(Resource::USERS);

        $item = new Item($button);
        $item->setResource($resource);

        return $item;
    }

    /**
     * @return Item
     * @throws \Opulence\Routing\Urls\UrlException
     */
    protected function createUserGroupItem(): Item
    {
        $text = 'admin:userGroups';
        $icon = 'group';

        $button   = $this->buttonFactory->createFromName($text, Route::USER_GROUPS_LIST, [], $icon);
        $resource = $this->getAdminResource(Resource::USER_GROUPS);

        $item = new Item($button);
        $item->setResource($resource);

        return $item;
    }

    /**
     * @return Item
     * @throws \Opulence\Routing\Urls\UrlException
     */
    protected function createLogoutItem(): Item
    {
        $text = 'admin:logout';
        $icon = 'settings_power';

        $button = $this->buttonFactory->createFromName($text, Route::LOGOUT_EXECUTE, [], $icon);

        return new Item($button);
    }

    /**
     * @param string $resource
     *
     * @return string
     */
    protected function getAdminResource(string $resource): string
    {
        return sprintf('admin_resource_%s', $resource);
    }
}
