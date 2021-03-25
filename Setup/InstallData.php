<?php

namespace Benny\Blog\Setup;

use Magento\Cms\Model\PageFactory;
use Magento\Framework\Setup\InstallDataInterface;
use Magento\Framework\Setup\ModuleContextInterface;
use Magento\Framework\Setup\ModuleDataSetupInterface;

class InstallData implements InstallDataInterface
{
    private $pageFactory;
    private $blockFactory;

    public function __construct(PageFactory $pageFactory)
    {
        $this->pageFactory = $pageFactory;
    }

    public function install(ModuleDataSetupInterface $setup, ModuleContextInterface $context)
    {
        // We're using a CMS page here to keep the Header & Footer of our Magento installation on the page.
        $cmsPageData = [
            'title' => 'N98 Blog',
            'page_layout' => '1column',
            'identifier' => 'blog',
            'content_heading' => 'N98 Blog',
            "content" => '{{block class="Benny\Blog\Block\Blog" name="n98-blog" template="Benny_Blog::blog.phtml"}}',
            'is_active' => 1,
            'stores' => [0],
            'sort_order' => 0
        ];
        $this->pageFactory->create()->setData($cmsPageData)->save();
    }
}
