<?php

namespace Benny\Blog\Block;

use Magento\Framework\View\Element\Template;

class Blog extends Template
{
    const BLOG_URL = "https://dev98.de/";

    protected $pageContent;
    protected $blogData = [];
    protected $dom;
    protected $curl;

    public function __construct(Template\Context $context)
    {
        // Init both the DOMDocument object for later and the curl_init function.
        $this->dom = new \DOMDocument();
        $this->curl = curl_init();
        parent::__construct($context);
    }

    public function fetchWebsite($websiteURL)
    {
        $this->curl = curl_init();
        // Fetch the website and return the corresponding HTML.
        curl_setopt($this->curl, CURLOPT_URL, $websiteURL);
        curl_setopt($this->curl, CURLOPT_FOLLOWLOCATION, 1);
        curl_setopt($this->curl, CURLOPT_RETURNTRANSFER, 1);
        return curl_exec($this->curl);
    }

    public function getBlogPreviewData()
    {
        // Load the HTML and define xPath, so we can use "getElementsByClassName" later with $xpath->query();
        libxml_use_internal_errors(true);

        // Load the document here, and parse it either as XML or HTML.
        $this->initDOMDocument("HTML", self::BLOG_URL);

        // Fetch all the article nodes.
        $articles = $this->dom->getElementsByTagName("article");

        foreach($articles as $key => $post) {
            $blogImage = $post->getElementsByTagName("img")->item(0);
            $urlPath = $post->getElementsByTagName("a")->item(0)->getAttribute("href");

            // In case the page has empty article nodes, jump to the next iteration.
            if(!isset($blogImage)) continue;

            // In case the href from the <a> elements is hardcoded, let's correct this.
            $urlPath = substr($urlPath, strpos($urlPath, "/", 10));

            // Using some xPath here to fetch our elements by classes for the frontend later.
            $author = $this->xpath->query("//div[@class='entry-meta']")[$key];
            $description = $this->xpath->query("//div[@class='entry-summary']")[$key];

            // Fill our array with data. Name has to be trimmed due to WordPress correcting HTML automatically.
            $this->blogData[] = [
                "title" => $post->getElementsByTagName("h2")->item(0)->nodeValue,
                "author" => $post->getElementsByTagName("a")->item(3)->nodeValue,
                "date" => $post->getElementsByTagName("a")->item(2)->nodeValue,
                "comments" => $post->getElementsByTagName("a")->item(4)->nodeValue,
                "description" => $this->getInnerHtml($description),
                "image" => $blogImage->getAttribute("src"),
                "url" => $urlPath
            ];
        }
        return $this->blogData;
    }

    protected function getInnerHtml($node)
    {
        // With this we can get actual HTML from DOMDocument, not only nodeValues.
        $innerHTML= '';
        foreach($node->childNodes as $child) {
            $innerHTML .= $child->ownerDocument->saveXML($child);
        }
        return $innerHTML;
    }

    public function initDOMDocument($type, $resourceURI)
    {
        // Either load XML via simplexml_load_string or load HTML and initialize DOMXpath.
        if($type === "XML") {
            $this->dom = simplexml_load_string($this->fetchWebsite($resourceURI));
        }
        else {
            $this->dom->loadHTML($this->fetchWebsite($resourceURI));
            $this->xpath = new \DOMXpath($this->dom);
        }
    }
    public function getFeedUrl()
    {
        return self::BLOG_URL;
    }
}
