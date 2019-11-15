<?php
/**
 * Html2Pdf Library - main class
 *
 * HTML => PDF converter
 * distributed under the OSL-3.0 License
 *
 * @package   Html2pdf
 * @author    Laurent MINGUET <webmaster@html2pdf.fr>
 * @copyright 2017 Laurent MINGUET
 */

namespace Library\Plugin;

use Spipu\Html2Pdf\Html2Pdf;
use Spipu\Html2Pdf\Parsing\HtmlLexer;
use Spipu\Html2Pdf\Parsing\Node;
use Spipu\Html2Pdf\Parsing\TagParser;
use Spipu\Html2Pdf\Parsing\TextParser;

class myHtml2Pdf extends Html2Pdf
{
    protected $myLexer = null;

    public function __construct(
        $orientation = 'P',
        $format = 'A4',
        $lang = 'fr',
        $unicode = true,
        $encoding = 'UTF-8',
        $margins = array(5, 5, 5, 8),
        $pdfa = false
    ) {
        parent::__construct($orientation, 
        $format, 
        $lang, 
        $unicode, 
        $encoding, 
        $margins, 
        $pdfa);
        $this->myLexer = new HtmlLexer();
    }

    public function setPageMargins($margins = null) {

    }

    public function getDefaultMargins() {
        return array(
            'left' => $this->_defaultLeft,
            'top' => $this->_defaultTop,
            'right' => $this->_defaultRight,
            'bottom' => $this->_defaultBottom
        );
    }

    public function getTagHeight($html) {
        $res = null;

        $html = $this->parsingHtml->prepareHtml($html);
        $html = $this->parsingCss->extractStyle($html);
        $this->parsingHtml->parse($this->myLexer->tokenize($html));
        $sub = $this->createSubHTML();
        if (in_array($this->parsingHtml->code[0]->getName(), array('page_footer'))) {
            $sub->parsingHtml->code = $this->parsingHtml->getLevel(0);
        }else {
            $sub->parsingHtml->code = $this->parsingHtml->code;
        }
        $sub->_makeHTMLcode();
        $res = $sub->_maxY;
        $this->_destroySubHTML($sub);

        return $res;
    }

    public function addNewPage($format = null, 
        $orientation = '', 
        $background = null, 
        $curr = null, 
        $resetPageNumber = false) {
        $this->_setNewPage($format, 
            $orientation, 
            $background, 
            $curr, 
            $resetPageNumber);
        return $this;
    }

    /**
     * display an image
     *
     * @access protected
     * @param  string $src
     * @param  boolean $subLi if true=image of a list
     * @return boolean depending on "isForOneLine"
     */
    protected function _drawImage($src, $subLi = false)
    {
        // get the size of the image
        // WARNING : if URL, "allow_url_fopen" must turned to "on" in php.ini
        $infos=@getimagesize($src);

        // if the image does not exist, or can not be loaded
        if (!is_array($infos) || count($infos)<2) {
            if ($this->_testIsImage) {
                $e = new ImageException('Unable to get the size of the image ['.$src.']');
                $e->setImage($src);
                throw $e;
            }

            // display a gray rectangle
            $src = null;
            $infos = array(16, 16);

            // if we have a fallback Image, we use it
            if ($this->_fallbackImage) {
                $src = $this->_fallbackImage;
                $infos = @getimagesize($src);

                if (count($infos)<2) {
                    $e = new ImageException('Unable to get the size of the fallback image ['.$src.']');
                    $e->setImage($src);
                    throw $e;
                }
            }
        }

        // if image is a string
        if (!is_file($src) && strpos($src, 'data:image') !== false) {
            list($stream, $data) = explode(",", $src);
            if (strpos($stream, 'base64') !== false) {
                $src = '@' . base64_decode($data);
            }
            else {
                $src = '@' . $data;
            }
        }

        // convert the size of the image in the unit of the PDF
        $imageWidth = $infos[0]/$this->pdf->getK();
        $imageHeight = $infos[1]/$this->pdf->getK();

        $ratio = $imageWidth / $imageHeight;

        // calculate the size from the css style
        if ($this->parsingCss->value['width'] && $this->parsingCss->value['height']) {
            $w = $this->parsingCss->value['width'];
            $h = $this->parsingCss->value['height'];
        } elseif ($this->parsingCss->value['width']) {
            $w = $this->parsingCss->value['width'];
            $h = $w / $ratio;
        } elseif ($this->parsingCss->value['height']) {
            $h = $this->parsingCss->value['height'];
            $w = $h * $ratio;
        } else {
            // convert px to pt
            $w = 72./96.*$imageWidth;
            $h = 72./96.*$imageHeight;
        }

        if (isset($this->parsingCss->value['max-width']) && $this->parsingCss->value['max-width'] < $w) {
            $w = $this->parsingCss->value['max-width'];
            if (!$this->parsingCss->value['height']) {
                // reprocess the height if not constrained
                $h = $w / $ratio;
            }
        }
        if (isset($this->parsingCss->value['max-height']) && $this->parsingCss->value['max-height'] < $h) {
            $h = $this->parsingCss->value['max-height'];
            if (!$this->parsingCss->value['width']) {
                // reprocess the width if not constrained
                $w = $h * $ratio;
            }
        }

        // are we in a float
        $float = $this->parsingCss->getFloat();

        // if we are in a float, but if something else if on the line
        // => make the break line (false if we are in "_isForOneLine" mode)
        if ($float && $this->_maxH && !$this->_tag_open_BR(array())) {
            return false;
        }

        // position of the image
        $x = $this->pdf->GetX();
        $y = $this->pdf->GetY();

        // if the image can not be put on the current line => new line
        if (!$float && ($x + $w>$this->pdf->getW() - $this->pdf->getrMargin()) && $this->_maxH) {
            if ($this->_isForOneLine) {
                return false;
            }

            // set the new line
            $hnl = max($this->_maxH, $this->parsingCss->getLineHeight());
            $this->_setNewLine($hnl);

            // get the new position
            $x = $this->pdf->GetX();
            $y = $this->pdf->GetY();
        }

        // if the image can not be put on the current page
        if (($y + $h>$this->pdf->getH() - $this->pdf->getbMargin()) && !$this->_isInOverflow) {
            // new page
            $this->_setNewPage();

            // get the new position
            $x = $this->pdf->GetX();
            $y = $this->pdf->GetY();
        }

        // correction for display the image of a list
        $hT = 0.80*$this->parsingCss->value['font-size'];
        if ($subLi && $h<$hT) {
            $y+=($hT-$h);
        }

        // add the margin top
        $yc = $y-$this->parsingCss->value['margin']['t'];

        // get the width and the position of the parent
        $old = $this->parsingCss->getOldValues();
        if ($old['width']) {
            $parentWidth = $old['width'];
            $parentX = $x;
        } else {
            $parentWidth = $this->pdf->getW() - $this->pdf->getlMargin() - $this->pdf->getrMargin();
            $parentX = $this->pdf->getlMargin();
        }

        // if we are in a gloat => adapt the parent position and width
        if ($float) {
            list($lx, $rx) = $this->_getMargins($yc);
            $parentX = $lx;
            $parentWidth = $rx-$lx;
        }

        // calculate the position of the image, if align to the right
        if ($parentWidth>$w && $float !== 'left') {
            if ($float === 'right' || $this->parsingCss->value['text-align'] === 'li_right') {
                $x = $parentX + $parentWidth - $w-$this->parsingCss->value['margin']['r']-$this->parsingCss->value['margin']['l'];
            }
        }

        // display the image
        if (!$this->_subPart && !$this->_isSubPart) {
            if ($src) {
                $this->pdf->Image($src, $x, $y, $w, $h, '', $this->_isInLink);
            } else {
                // rectangle if the image can not be loaded
                $this->pdf->SetFillColorArray(array(240, 220, 220));
                $this->pdf->Rect($x, $y, $w, $h, 'F');
            }
        }

        // apply the margins
        $x-= $this->parsingCss->value['margin']['l'];
        $y-= $this->parsingCss->value['margin']['t'];
        $w+= $this->parsingCss->value['margin']['l'] + $this->parsingCss->value['margin']['r'];
        $h+= $this->parsingCss->value['margin']['t'] + $this->parsingCss->value['margin']['b'];

        if ($float === 'left') {
            // save the current max
            $this->_maxX = max($this->_maxX, $x+$w);
            $this->_maxY = max($this->_maxY, $y+$h);

            // add the image to the margins
            $this->_addMargins($float, $x, $y, $x+$w, $y+$h);

            // get the new position
            list($lx, $rx) = $this->_getMargins($yc);
            $this->pdf->SetXY($lx, $yc);
        } elseif ($float === 'right') {
            // save the current max. We don't save the X because it is not the real max of the line
            $this->_maxY = max($this->_maxY, $y+$h);

            // add the image to the margins
            $this->_addMargins($float, $x, $y, $x+$w, $y+$h);

            // get the new position
            list($lx, $rx) = $this->_getMargins($yc);
            $this->pdf->SetXY($lx, $yc);
        } else {
            // set the new position at the end of the image
            $this->pdf->SetX($x+$w);

            // save the current max
            $this->_maxX = max($this->_maxX, $x+$w);
            $this->_maxY = max($this->_maxY, $y+$h);
            $this->_maxH = max($this->_maxH, $h);
        }

        return true;
    }

}
