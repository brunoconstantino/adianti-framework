<?php
/**
 * Classe abstrata para gr�fico
 */
abstract class TChart
{
    protected $title;
    protected $chartDesigner;
    protected $outputPath;
    protected $width;
    protected $height;
    
    /**
     * M�todo construtor
     * @param $chartDesigner objeto TChartDesigner
     */
    public function __construct(TChartDesigner $chartDesigner)
    {
        $this->chartDesigner = $chartDesigner;
    }
    
    /**
     * Define o t�tulo do gr�fico
     * @param $title t�tulo do gr�fico
     */
    public function setTitle($title)
    {
        $this->title = $title;
    }
    
    /**
     * Define o tamanho do gr�fico
     * @param $width  largura
     * @param $height altura
     */
    public function setSize($width, $height)
    {
        $this->width  = $width;
        $this->height = $height;
    }
    
    /**
     * Define o arquivo de sa�da do gr�fico
     * @param $outputPath localiza��o do arquivo de sa�da
     */
    public function setOutputPath($outputPath)
    {
        $this->outputPath = $outputPath;
    }
    
    /**
     * M�todo abstrato para gera��o do gr�fico
     */
    abstract public function generate();
}
?>