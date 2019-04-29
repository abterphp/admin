<?php

declare(strict_types=1);

namespace AbterPhp\Admin\Events\Listeners;

use AbterPhp\Admin\Constant\Routes;
use AbterPhp\Framework\Constant\Html5;
use AbterPhp\Framework\Events\NavigationReady;
use AbterPhp\Framework\Html\Component\ButtonFactory;
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
    protected $session;

    /** @var ButtonFactory */
    protected $buttonFactory;

    /**
     * NavigationRegistrar constructor.
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

        $dropdown = new Dropdown();
        $dropdown[] = $this->createUserItem();
        $dropdown[] = $this->createUserGroupItem();

        $mainItem   = $this->createUserItem();
        $mainItem->setIntent(Item::INTENT_DROPDOWN);
        $mainItem->setAttribute(Html5::ATTR_ID, 'nav-users');
        $mainItem[0]->setAttribute(Html5::ATTR_HREF, 'javascript:void(0);');
        $mainItem[1] = $dropdown;

        $logout = $this->createLogoutItem();

        $navigation->addItem($mainItem, static::DEFAULT_BASE_WEIGHT);
        $navigation->addItem($logout, static::DEFAULT_BASE_WEIGHT);
    }

    /**
     * @param Navigation $navigation
     */
    protected function insertFirstItem(Navigation $navigation)
    {
        $firstItem = new Item(null, [UserBlock::class]);

        $firstItem[] = $this->createUserBlock();
        $firstItem[] = $this->createDropdown();

        $navigation->addItem($firstItem, static::FIRST_ITEM_WEIGHT);
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
     */
    protected function createDropdown(): Dropdown
    {
        $text = 'framework:logout';

        $button = $this->buttonFactory->createFromName($text, Routes::ROUTE_LOGOUT, []);

        return new Dropdown(new Item($button));
    }

    /**
     * @return Item
     * @throws \Opulence\Routing\Urls\UrlException
     */
    protected function createUserItem(): Item
    {
        $text = 'admin:users';
        $icon = 'person';

        $button   = $this->buttonFactory->createFromName($text, Routes::ROUTE_USERS, [], $icon);
        $resource = $this->getAdminResource(Routes::ROUTE_USERS);

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

        $button   = $this->buttonFactory->createFromName($text, Routes::ROUTE_USER_GROUPS, [], $icon);
        $resource = $this->getAdminResource(Routes::ROUTE_USER_GROUPS);

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

        $button   = $this->buttonFactory->createFromName($text, Routes::ROUTE_LOGOUT, [], $icon);
        $resource = $this->getAdminResource(Routes::ROUTE_LOGOUT);

        $item = new Item($button);
        $item->setResource($resource);

        return $item;
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
