<li class="has-sub menu-1-migrate">
    <a href="javascript:;">
        <b class="caret pull-right"></b>
        迁移<span>数据</span>
    </a>
    <ul class="sub-menu">
        <li class="menu-2-customer"><a href="<?= Url::to([
                '/data_migration/migrate-customer/migrate',
                'menu'=>Json::encode([
                    'menu-1-migrate',
                    'menu-2-customer'
                ]),
            ])?>">客户迁移</a></li>
        <li class="menu-2-customer-contact"><a href="<?= Url::to([
                '/data_migration/migrate-customer-contact/migrate',
                'menu'=>Json::encode([
                    'menu-1-migrate',
                    'menu-2-customer-contact'
                ]),
            ])?>">客户联系人迁移</a></li>
        <li class="menu-2-project"><a href="<?= Url::to([
                '/data_migration/migrate-project/migrate',
                'menu'=>Json::encode([
                    'menu-1-migrate',
                    'menu-2-project'
                ]),
            ])?>">项目迁移</a></li>
    </ul>
</li>
