<?php
/**
 * Classe para gera��o de gr�ficos de torta
 */
final class TPieChart extends TChart
{
    private $data;
    
    /**
     * M�todo construtor
     * @param $chartDesigner objeto TChartDesigner
     */
    public function __construct(TChartDesigner $chartDesigner)
    {
        parent::__construct($chartDesigner);
        
        $this->data = array();
    }
    
    /**
     * Adiciona dados ao gr�fico
     * @param $legend palavra para legenda
     * @param $value  valor
     */
    public function addData($legend, $value)
    {
        $this->data[$legend] = $value;
    }
    
    /**
     * Gera o gr�fico
     */
    public function generate()
    {
        $this->chartDesigner->drawPieChart($this->title, $this->data,
                                           $this->width, $this->height, $this->outputPath);
    }
}

?>