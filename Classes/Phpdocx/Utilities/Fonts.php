<?php

namespace Phpdocx\Utilities;

/**
 * Font handled
 *
 * @category   Phpdocx
 * @package    utilities
 * @copyright  Copyright (c) Narcea Producciones Multimedia S.L.
 *             (http://www.2mdc.com)
 * @license    phpdocx LICENSE
 * @link       http://www.phpdocx.com
 */
class Fonts
{
    /**
     * Generates an ODTTF from a TTF
     * 
     * @param mixed $source TTF source
     * @return stream ODTTF
     */
    public function generateODTTF($source)
    {
        // generate a 16 byte GUID to be used to perform obfuscation
        $guid = $this->generateGUID();

        $dataUid = array_reverse(str_split($guid['rawguid'], 2));

        $fontContent = file_get_contents($source);

        // get the file content as HEX
        $hex = unpack('H*', $fontContent);
        $dataHex = str_split($hex[1], 2);

        // obfuscate the font from the GUID and the font content
        for ($i = 0; $i < 16 ; $i++) {
            $dataHex[$i] = dechex(hexdec($dataHex[$i]) ^ hexdec($dataUid[$i]));
            $dataHex[$i + 16] = dechex(hexdec($dataHex[$i + 16]) ^ hexdec($dataUid[$i]));
        }

        // new ODTTF file
        $binaryFile = '';
        foreach ($dataHex as $data) {
            $binaryFile .= pack('H*', $data);
        }
        
        return array('odttf' => $binaryFile, 'guid' => $guid['guid']);
    }

    /**
     * Generates a new font entry for fontTable.xml
     * @param string $guid
     * @param string $id
     * @param string $fontName
     * @param array $options
     * @return string Font entry
     */
    public function generateFontEntry($guid, $rid, $fontName, $options = array())
    {
        $fontEntry = '
            <w:font w:name="' . $fontName . '">
                <w:charset w:val="' . $options['charset'] . '"/>
                <w:' . $options['styleEmbeding'] . ' r:id="' . $rid . '" w:fontKey="' . $guid . '"/>
            </w:font>';

        return $fontEntry;
    }

    /**
     * Generates a UID
     * @return array GUID and RAW GUID
     */
    protected function generateGUID()
    {
        $charid = strtoupper(md5(uniqid(rand(), true)));
        $hyphen = chr(45); // '-'
        $uuid = chr(123) // '{'
            . substr($charid, 0, 3) . '14A78' . $hyphen
            . '8E89' . $hyphen
            . '426F' . $hyphen
            . '90D8' . $hyphen
            . '5CD89AEFD' . substr($charid, 20, 3)
            . chr(125); // '}'

        // force an UUID, as DOCX allows using the same UUID for more than font
        //$uuid = '{23BA4462-8E89-426F-90D8-59D98759585D}';

        return array('guid' => $uuid, 'rawguid' => str_replace(array('{', '}', '-'), '', $uuid));
    }
}