<?php
/**
 * Classe para gera��o de gr�ficos de linhas
 */
final class TLineChart extends TChart
{
    private $data;
    private $xlabels;
    private $ylabel;
    
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
     * Define os r�tulos do eixo X do gr�fico
     * @param $labels vetor com r�tulos
     */
    public function setXLabels($labels)
    {
        $this->xlabels = $labels;
    }
    
    /**
     * Define o r�tulo do eixo Y do gr�fico
     * @param $label r�tulo
     */
    public function setYLabel($label)
    {
        $this->ylabel = $label;
    }
    
    /**
     * Adiciona uma s�rie de dados ao gr�fico
     * @param $legend legenda para a s�rie de dados
     * @param $data s�rie de dados
     */
    public function addData($legend, $data)
    {
        $this->data[$legend] = $data;
    }
    
    /**
     * Gera o gr�fico
     */
    public function generate()
    {
        $this->chartDesigner->drawLineChart($this->title, $this->data, $this->xlabels, $this->ylabel, 
                                            $this->width, $this->height, $this->outputPath);
    }
}

?>