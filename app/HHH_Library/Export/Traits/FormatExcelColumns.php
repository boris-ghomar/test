<?php

namespace App\HHH_Library\Export\Traits;


trait FormatExcelColumns
{

    /*
    source:
    https://github.com/shuchkin/simplexlsxgen

    ['Normal', '12345.67'],
    ['Bold', '<b>12345.67</b>'],
    ['Italic', '<i>12345.67</i>'],
    ['Underline', '<u>12345.67</u>'],
    ['Strike', '<s>12345.67</s>'],
    ['Bold + Italic', '<b><i>12345.67</i></b>'],
    ['Hyperlink', 'https://github.com/shuchkin/simplexlsxgen'],
    ['Italic + Hyperlink + Anchor', '<i><a href="https://github.com/shuchkin/simplexlsxgen">SimpleXLSXGen</a></i>'],
    ['Green', '<style color="#00FF00">12345.67</style>'],
    ['Bold Red Text', '<b><style color="#FF0000">12345.67</style></b>'],
    ['Size 32 Font', '<style font-size="32">Big Text</style>'],
    ['Blue Text and Yellow Fill', '<style bgcolor="#FFFF00" color="#0000FF">12345.67</style>'],
    ['Border color', '<style border="#000000">Black Thin Border</style>'],
    ['<top>Border style</top>','<style border="medium"><wraptext>none, thin, medium, dashed, dotted, thick, double, hair, mediumDashed, dashDot,mediumDashDot, dashDotDot, mediumDashDotDot, slantDashDot</wraptext></style>'],
    ['Border sides', '<style border="none dotted#0000FF medium#FF0000 double">Top No + Right Dotted + Bottom medium + Left double</style>'],
    ['Left', '<left>12345.67</left>'],
    ['Center', '<center>12345.67</center>'],
    ['Right', '<right>Right Text</right>'],
    ['Center + Bold', '<center><b>Name</b></center>'],
    ['Row height', '<style height="50">Row Height = 50</style>'],
    ['Top', '<style height="50"><top>Top</top></style>'],
    ['Middle + Center', '<style height="50"><middle><center>Middle + Center</center></middle></style>'],
    ['Bottom + Right', '<style height="50"><bottom><right>Bottom + Right</right></bottom></style>'],
    ['<center>MERGE CELLS MERGE CELLS MERGE CELLS MERGE CELLS MERGE CELLS</center>', null],
    ['<top>Word wrap</top>', "<wraptext>Lorem Ipsum is simply dummy text of the printing and typesetting industry. Lorem Ipsum has been the industry's standard dummy text ever since the 1500s, when an unknown printer took a galley of type and scrambled it to make a type specimen book</wraptext>"],

    */


    /**
     * Cell display format
     *
     * @param  mixed $data
     * @param  mixed $format
     * @return string
     */
    public function formatCell(mixed $data, string $format): string
    {

        return sprintf("<middle><%s>%s</%s></middle>", $format, $data, $format);
    }

    /************* Single Styles ******************/

    /**
     * Cell style bold
     *
     * @param  mixed $data
     * @return string
     */
    public function cellStyleBold(mixed $data): string
    {
        return $this->formatCell($data, "b");
    }

    /**
     * Cell style italic
     *
     * @param  mixed $data
     * @return string
     */
    public function cellStyleItalic(mixed $data): string
    {
        return $this->formatCell($data, "i");
    }

    /**
     * Cell style unserline
     *
     * @param  mixed $data
     * @return string
     */
    public function cellStyleUnderline(mixed $data): string
    {
        return $this->formatCell($data, "u");
    }

    /**
     * Cell style strike
     *
     * @param  mixed $data
     * @return string
     */
    public function cellStyleStrike(mixed $data): string
    {
        return $this->formatCell($data, "s");
    }

    /**
     * Cell style left align
     *
     * @param  mixed $data
     * @return string
     */
    public function cellStyleLeft(mixed $data): string
    {
        return $this->formatCell($data, "left");
    }

    /**
     * Cell style right align
     *
     * @param  mixed $data
     * @return string
     */
    public function cellStyleRight(mixed $data): string
    {
        return $this->formatCell($data, "right");
    }

    /**
     * Cell style center align
     *
     * @param  mixed $data
     * @return string
     */
    public function cellStyleCenter(mixed $data): string
    {
        return $this->formatCell($data, "center");
    }

    /**
     * Cell style wrap text
     *
     * @param  mixed $data
     * @return string
     */
    public function cellStyleWrapText(mixed $data): string
    {
        return $this->formatCell($data, "wraptext");
    }


    /************* Single Styles END ******************/


    /************* Merged Styles ******************/

    /**
     * Cell style bold + italic
     *
     * @param  mixed $data
     * @return string
     */
    public function cellStyleBoldItalic(mixed $data): string
    {
        return $this->cellStyleBold($this->cellStyleItalic($data));
    }

    /**
     * Cell style center + bold
     *
     * @param  mixed $data
     * @return string
     */
    public function cellStyleCenterBold(mixed $data): string
    {
        return $this->cellStyleCenter($this->cellStyleBold($data));
    }

    /************* Merged Styles END ******************/
}
