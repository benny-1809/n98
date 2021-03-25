<?php

namespace Benny\Blog\Controller\Blog;

use Magento\Framework\App\Action\Action;
use Magento\Framework\App\Action\Context;
use Magento\Framework\View\Result\PageFactory;
use Benny\Blog\Block\Blog;

class Fetch extends Action
{
    protected $blog;
    protected $json;

    public function __construct(Context $context, Blog $blog)
    {
        // Injecting our Block here to simply access the fetchWebsite function from it.
        parent::__construct($context);
        $this->blog = $blog;
    }

    public function execute()
    {
        // We're passing the blogID here to fetch the feeds contents for the encoded full content.
        $blogId = $this->getRequest()->getParam("id");
        $fetchedWebsite = $this->blog->fetchWebsite(Blog::BLOG_URL . "feed");

        // Finally fetching the blog data since the api is locked for some reason.
        $xml = simplexml_load_string($fetchedWebsite,"SimpleXMLElement", LIBXML_NOCDATA);

        // God knows why this will not work without casting the key to an integer...
        echo $xml->channel->item[(int) $blogId]->children("content", true)->encoded;
    }
}
