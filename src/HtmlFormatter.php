<?php
/**
 * HtmlFormatter class code.
 * 
 * @file
 * @category DomUtils
 * @package  HtmlFormatter
 * @author   Salvador Perez <salvadorperezd@gmail.com>
 * @license  https://opensource.org/licenses/MIT MIT
 * @link     https://github.com/SalvadorP/HtmlFormatter
 */

namespace src\Services;

use DOMDocument;
use DOMElement;
use DOMXPath;

/**
 * Class HtmlFormatter
 *
 * @category DomUtils
 * @package  HtmlFormatter
 * @author   Salvador Perez <salvadorperezd@gmail.com>
 * @license  https://opensource.org/licenses/MIT MIT
 * @link     https://github.com/SalvadorP/HtmlFormatter
 */
class HtmlFormatter
{
  
    /**
     * The DOM Document
     *
     * @var DOMDocument
     */
    protected $dom = '';
  
    /**
     * XPATH for the DOM Filtering
     *
     * @var DOMXPath
     */
    protected $xpath = '';
  
    /**
     * The processed HTML code for the final quick guide.
     *
     * @var string
     */
    protected $processedHTML = '';
  
    /**
     * Class Constructor.
     *
     * @param string $file HTML file path.
     */
    function __construct($file)
    {
        $this->dom = new DOMDocument();
        $internalErrorsState = libxml_use_internal_errors(true);
        $text = $this->getHtmlWithoutWordBlankSpaces($file);
        $this->dom->loadHTML($text);
        libxml_use_internal_errors($internalErrorsState);
        $this->xpath = new DOMXPath($this->dom);
        $this->processedHTML = '';
    }
  
    /**
     * Processes the uploaded File and retrieves the relevant HTML code.
     *
     * @return string The quick guide code.
     */
    function processFileAndGetHTML()
    {
        $this->incrementHeaderTags();
        $this->removeAttributesFromTag('img', ['height', 'width']);
        $tagAttributes = ['class' => ['img-responsive img-rounded']];
        $this->addAttributesToTag('img', $tagAttributes);
        $this->getWordSectionContents();
        return $this->processedHTML;
    }
  
    /**
     * Gets the HTML of the file and removes the word blank spaces.
     *
     * @param string $file File path of the uploaded HTML file.
     *
     * @return string       HTML contents of the file without the word blank spaces.
     */
    function getHtmlWithoutWordBlankSpaces($file)
    {
        $contents = file_get_contents($file);
        $search = "<p class=MsoNormal><span lang=EN-GB " .
        "style='mso-ansi-language:EN-GB'>&nbsp;</span></p>";
        return str_replace($search, '', $contents);
    }
  
    /**
     * Stringifies the inner HTML of the DOM Element.
     *
     * @param DOMElement $element The DOM Element.
     *
     * @return string The contens of the DOM Element as a string.
     */
    function DOMinnerHTML(DOMElement $element)
    {
        $innerHTML = "";
        $children  = $element->childNodes;
        foreach ($children as $child) {
            $innerHTML .= $element->ownerDocument->saveHTML($child);
        }
        return $innerHTML;
    }
  
    /**
     * Gets all the tags indicated by the filter H and increment them by one
     *
     * @param integer $totalHeaderTags Total of header tags.
     *
     * @return void
     */
    function incrementHeaderTags($totalHeaderTags = 20)
    {
        $filter = '(';
        for ($i = 1; $i <= $totalHeaderTags; $i++) {
            $filter .= '//h' . $i . '|';
        }
        $filter = trim($filter, '|') . ')';
    
        $elements = $this->xpath->query($filter);
        foreach ($elements as $index => $element) {
            $tag = $element->tagName;
            $tagValue = filter_var($tag, FILTER_SANITIZE_NUMBER_INT);
            $tagValue++;
            $tag = 'h'.$tagValue;
            $this->domRenameElement($element, $tag);
        }
    }
  
    /**
     * Gets the contents of the WordSections divs of the html page.
     *
     * @param integer $number Number of WordSections to search.
     *
     * @return string The HTML code with the divs WordSections.
     */
    function getWordSectionContents($number = 5)
    {
        $this->processedHTML = '';
        for ($i = 1; $i <= $number; $i++) {
            $class="WordSection" . $i;
            $query = "//*[contains(@class, '$class')]";
            $nodes = $this->xpath->query($query);
      
            foreach ($nodes as $node) {
                // Better than C14N!.
                $this->processedHTML .= $node->ownerDocument->saveHTML($node); 
            }
        }
    }
  
    /**
     * Removes all childs of the element, and the element.
     *
     * @param string      $tag HTML tag to remove from the DOM.
     * @param DOMDocument $dom The DOM Document to iterate.
     *
     * @return void
     */
    function removeElementsByTagName($tag, $dom)
    {
        $list = $this->dom->getElementsByTagName($tag);
        while ($node = $list->item(0)) {
            $node->parentNode->removeChild($node);
        }
    }
  
    /**
     * Renames a node in a DOM Document.
     *
     * @param DOMElement $node DOM's node.
     * @param string     $name Name of the DOM's node.
     *
     * @return DOMNode
     */
    function domRenameElement(DOMElement $node, $name)
    {
        $renamed = $node->ownerDocument->createElement($name);
    
        foreach ($node->attributes as $attribute) {
            $renamed->setAttribute($attribute->nodeName, $attribute->nodeValue);
        }
    
        while ($node->firstChild) {
            $renamed->appendChild($node->firstChild);
        }
    
        return $node->parentNode->replaceChild($renamed, $node);
    }
  
    /**
     * Gets all specified tags and remove its attributes
     *
     * @param string $tag        Tag name
     * @param array  $attributes Attributes to remove from the tag
     *
     * @return void
     */
    function removeAttributesFromTag($tag, $attributes)
    {
        $filter = '(//' . $tag . ')';
        $elements = $this->xpath->query($filter);
        foreach ($elements as $index => $element) {
            $tag = $element->tagName;
            foreach ($attributes as $attr) {
                if ($element->hasAttribute($attr)) {
                    $element->removeAttribute($attr);
                }
            }
        }
    }
    
    /**
     * Gets all specified tags and add the specific attribute
     *
     * @param string $tag        Tag name
     * @param array  $attributes Attributes to remove from the tag
     *
     * @return void
     */
    function addAttributesToTag($tag, $attributes)
    {
        $filter = '(//' . $tag . ')';
        $elements = $this->xpath->query($filter);
        foreach ($elements as $index => $element) {
            $tag = $element->tagName;
            foreach ($attributes as $attr => $values) {
                foreach ($values as $value) {
                    $element->setAttribute($attr, $value);
                }
            }
        }
    }
  
    /**
     * Gets all the information of each header
     *
     * @return array Array containing all the header information.
     */
    function getHeaderTags()
    {
        $filter = '(';
        for ($i = 1; $i <= 20; $i++) {
            $filter .= '//h' . $i . '|';
        }
        $filter = trim($filter, '|') . ')';
    
        $elements = $this->xpath->query($filter);
        $tags = [];
        foreach ($elements as $index => $element) {
            $level = filter_var($element->tagName, FILTER_SANITIZE_NUMBER_INT) - 1;
            $innerHtml = $this->DOMinnerHTML($element);
      
            $tags[$index]['header'] = $element->tagName;
            $tags[$index]['level'] = $level;
            $tags[$index]['name'] = trim(strip_tags($innerHtml));
            $tags[$index]['content'] = $innerHtml;
            $tags[$index]['toc'] = $this->getTOCFromTag($innerHtml);
        }
        return $tags;
    }
  
    /**
     * Obtains the _TOCXXX code created by word.
     *
     * @param string $html String containing the html to process.
     *
     * @return array Array with all the tocs contained in the HTML
     */
    function getTOCFromTag($html)
    {
        // TODO Getting allways the first one, investigate why sometimes have more.
        $tocs = [];
        $toc = '';
        if (preg_match("/_Toc[0-9]*/i", $html, $tocs)) {
            $toc = current($tocs);
        }
        return $toc;
    }
}
