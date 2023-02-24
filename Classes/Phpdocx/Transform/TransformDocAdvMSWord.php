<?php

namespace Phpdocx\Transform;

use Phpdocx\Logger\PhpdocxLogger;

/**
 * Transform documents using native PHP classes
 *
 * @category   Phpdocx
 * @package    trasform
 * @copyright  Copyright (c) Narcea Producciones Multimedia S.L.
 *             (http://www.2mdc.com)
 * @license    phpdocx LICENSE
 * @link       https://www.phpdocx.com
 */

require_once dirname(__FILE__) . '/TransformDocAdv.php';

class TransformDocAdvMSWord extends TransformDocAdv
{
    /**
     * Calculate and return the document statistics
     * 
     * @param string $source DOCX document
     * @param array $options :
     *   'selectedContent' (string) : documents or active (default)
     * @return array
     */
    public function getStatistics($source, $options = array())
    {
        if (!file_exists($source)) {
            PhpdocxLogger::logger('The file not exist', 'fatal');
        }

        // statistics
        $statistics = array();

        // start a Word instance
        $MSWordInstance = new \COM("word.application") or exit('Check that PHP COM is enabled and a working copy of Word is installed.');

        // check that the version of MS Word is 12 or higher
        if ($MSWordInstance->Version >= 12) {
            // hide MS Word
            $MSWordInstance->Visible = 0;

            // open the source document
            $MSWordInstance->Documents->Open($source);

            if (isset($options['selectedContent']) && $options['selectedContent'] == 'documents') {
                $statistics['Characters'] = $MSWordInstance->Documents[1]->ComputeStatistics(3);
                $statistics['CharactersWithSpaces'] = $MSWordInstance->Documents[1]->ComputeStatistics(5);
                $statistics['Lines'] = $MSWordInstance->Documents[1]->ComputeStatistics(1);
                $statistics['Pages'] = $MSWordInstance->Documents[1]->ComputeStatistics(2);
                $statistics['Paragraphs'] = $MSWordInstance->Documents[1]->ComputeStatistics(4);
                $statistics['Words'] = $MSWordInstance->Documents[1]->ComputeStatistics(0);

                // close Word
                $MSWordInstance->Documents[1]->Close();
            } else {
                $statistics['Characters'] = $MSWordInstance->ActiveDocument->ComputeStatistics(3);
                $statistics['CharactersWithSpaces'] = $MSWordInstance->ActiveDocument->ComputeStatistics(5);
                $statistics['Lines'] = $MSWordInstance->ActiveDocument->ComputeStatistics(1);
                $statistics['Pages'] = $MSWordInstance->ActiveDocument->ComputeStatistics(2);
                $statistics['Paragraphs'] = $MSWordInstance->ActiveDocument->ComputeStatistics(4);
                $statistics['Words'] = $MSWordInstance->ActiveDocument->ComputeStatistics(0);

                // close Word
                $MSWordInstance->ActiveDocument->Close();
            }
        } else {
            exit('The version of Word should be 12 (Word 2007) or higher');
        }
        $MSWordInstance->Quit();

        $MSWordInstance = null;

        return $statistics;
    }

    /**
     * Transform documents:
     *     DOCX to PDF, DOC
     *     PDF to DOCX, DOC
     *     DOC to DOCX, PDF
     *
     * @access public
     * @param $source
     * @param $target
     * @param array $options :
     *   'selectedContent' (string) : documents or active (default)
     *   'toc' (bool) : false (default) or true. It generates the TOC before transforming the document
     * @return void
     */
    public function transformDocument($source, $target, $options = array())
    {
        $allowedExtensionsSource = array('doc', 'docx', 'pdf');
        $allowedExtensionsTarget = array('doc', 'docx', 'pdf');

        $filesExtensions = $this->checkSupportedExtension($source, $target, $allowedExtensionsSource, $allowedExtensionsTarget);

        $code = array(
            'doc' => new \VARIANT(0, VT_I4),
            'docx' => new \VARIANT(16, VT_I4),
            'pdf' => new \VARIANT(17, VT_I4),
        );

        // start a Word instance
        $MSWordInstance = new \COM("word.application") or exit('Please check that PHP COM is enabled and a working copy of Word is installed.');

        // check that the version of MS Word is 12 or higher
        if ($MSWordInstance->Version >= 12) {
            // hide MS Word
            $MSWordInstance->Visible = 0;

            // open the source document
            $MSWordInstance->Documents->Open($source);

            if (isset($options['selectedContent']) && $options['selectedContent'] == 'documents') {
                // generate the TOC content
                if (isset($options['toc']) && $options['toc']) {
                    $MSWordInstance->Documents[1]->TablesOfContents(1);
                }

                // save the target document
                $MSWordInstance->Documents[1]->SaveAs($target, $code[$filesExtensions['targetExtension']]);

                // close Word
                $MSWordInstance->Documents[1]->Close();
            } else {
                // generate the TOC content
                if (isset($options['toc']) && $options['toc']) {
                    $MSWordInstance->ActiveDocument->TablesOfContents(1);
                }

                // save the target document
                $MSWordInstance->ActiveDocument->SaveAs($target, $code[$filesExtensions['targetExtension']]);

                // close Word
                $MSWordInstance->ActiveDocument->Close();
            }
        } else {
            exit('The version of Word should be 12 (Word 2007) or higher');
        }
        $MSWordInstance->Quit();

        $MSWordInstance = null;
    }

}
