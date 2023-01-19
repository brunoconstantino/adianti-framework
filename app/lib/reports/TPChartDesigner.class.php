<?php
/**
 * Classe que implementa o desenho de gr�ficos para a biblioteca pChart
 */
final class TPChartDesigner extends TChartDesigner
{
    /**
     * Desenha um gr�fico de linhas
     * @param $title t�tulo do graico
     * @param $data matriz contendo as s�ries de dados
     * @param $xlabels vetor contendo os r�tulos do eixo X
     * @param $ylabel  r�tulo do eixo Y
     * @param $width largura do gr�fico
     * @param $height altura do gr�fico
     * @param $outputPath caminho de sa�da do gr�fico
     */
    public function drawLineChart($title, $data, $xlabels, $ylabel, $width, $height, $outputPath)
    {
        // cria o modelo de dados
        $modelo = new pData;
        
        foreach ($data as $legend => $serie)
        {
            $newdata = array();
            foreach ($serie as $value)
            {
                $newdata[$legend][] = $value == NULL ? VOID : $value;
            }
            
            $modelo->addPoints($newdata[$legend], $legend);
        }
        
        $modelo->setAxisName(0, $ylabel); 
        $modelo->addPoints($xlabels, "Labels");
        $modelo->setAbscissa("Labels"); 
        
        // cria o objeto que ir� conter a imagem do gr�fico
        $imagem = new pImage($width, $height, $modelo); 
        
        // adiciona uma borda na forma de um ret�ngulo dentro da imagem
        $imagem->drawRectangle(0, 0, $width-1, $height-1, array("R"=>0,"G"=>0,"B"=>0)); 
          
        // escreve um t�tulo dentro do gr�fico
        $imagem->setFontProperties(array("FontName"=>"app/lib/pchart/fonts/Forgotte.ttf","FontSize"=>11)); 
        $imagem->drawText(60, 35, $title, array("FontSize"=>20,"Align"=>TEXT_ALIGN_BOTTOMLEFT)); 
        
        // define a fonte dos dados do gr�fico 
        $imagem->setFontProperties(array("FontName"=>"app/lib/pchart/fonts/pf_arma_five.ttf","FontSize"=>6)); 
        
        // define a �rea do gr�fico
        $imagem->setGraphArea(60, 40, $width-50, $height-30);
        
        // define a escala do gr�fico
        $scaleSettings = array("XMargin"=>10,"YMargin"=>10,"CycleBackground"=>TRUE,
                               "GridR"=>200,"GridG"=>200,"GridB"=>200);
        $imagem->drawScale($scaleSettings); 
        
        // liga a suaviza��o de linhas
        $imagem->Antialias = TRUE; 
        
        // desenha o gr�fico de linhas
        $imagem->drawLineChart(array('DisplayValues'=>TRUE)); 
        
        // desenha a legenda do gr�fico 
        $imagem->drawLegend(60, $height-13, array("Style"=>LEGEND_NOBORDER,"Mode"=>LEGEND_HORIZONTAL)); 
        
        // grava o gr�fico em um arquivo
        $imagem->render($outputPath); 
    }
    
    /**
     * Desenha um gr�fico de barras
     * @param $title t�tulo do graico
     * @param $data matriz contendo as s�ries de dados
     * @param $xlabels vetor contendo os r�tulos do eixo X
     * @param $ylabel  r�tulo do eixo Y
     * @param $width largura do gr�fico
     * @param $height altura do gr�fico
     * @param $outputPath caminho de sa�da do gr�fico
     */
    public function drawBarChart($title,  $data, $xlabels, $ylabel, $width, $height, $outputPath)
    {
        // cria o modelo de dados
        $modelo = new pData;
           
        foreach ($data as $legend => $serie)
        {
            $newdata = array();
            foreach ($serie as $value)
            {
                $newdata[$legend][] = $value == NULL ? VOID : $value;
            }
            
            $modelo->addPoints($newdata[$legend], $legend);
        }
        
        $modelo->setAxisName(0, $ylabel);
        $modelo->addPoints($xlabels, "Labels");
        $modelo->setAbscissa("Labels");
        
        // cria o objeto que ir� conter a imagem do gr�fico
        $imagem = new pImage($width, $height, $modelo); 
        
        // desliga a suaviza��o de linhas
        $imagem->Antialias = FALSE; 
        
        // adiciona uma borda na forma de um ret�ngulo dentro da imagem 
        $imagem->drawRectangle(0,0, $width-1, $height-1, array("R"=>0,"G"=>0,"B"=>0)); 
        
        // escreve um t�tulo dentro do gr�fico
        $imagem->setFontProperties(array("FontName"=>"app/lib/pchart/fonts/Forgotte.ttf","FontSize"=>11)); 
        $imagem->drawText(60,35, $title,array("FontSize"=>20,"Align"=>TEXT_ALIGN_BOTTOMLEFT));
        
        // define a fonte dos dados do gr�fico
        $imagem->setFontProperties(array("FontName"=>"app/lib/pchart/fonts/pf_arma_five.ttf","FontSize"=>6));
        
        // define a �rea do gr�fico 
        $imagem->setGraphArea(60, 40, $width-50, $height-30); 
        
        // define a escala do gr�fico
        $scaleSettings = array("GridR"=>200,"GridG"=>200,"GridB"=>200,"CycleBackground"=>TRUE); 
        $imagem->drawScale($scaleSettings); 
        
        // desenha a legenda do gr�fico
        $imagem->drawLegend(60,$height-13,array("Style"=>LEGEND_NOBORDER,"Mode"=>LEGEND_HORIZONTAL)); 
        
        // cria uma sombra nas barras do gr�fico  
        $imagem->setShadow(TRUE,array("X"=>1,"Y"=>1,"R"=>0,"G"=>0,"B"=>0,"Alpha"=>10)); 
        
        // desenha o gr�fico de barras
        $imagem->drawBarChart(array("DisplayPos"=>LABEL_POS_INSIDE, // valores dentro das barras
                                    "DisplayValues"=>TRUE,"Surrounding"=>200)); 
        
        // grava o gr�fico em um arquivo
        $imagem->render($outputPath);
    }
    
    /**
     * Desenha um gr�fico de torta
     * @param $title t�tulo do graico
     * @param $data vetor contendo os dados do gr�fico
     * @param $width largura do gr�fico
     * @param $height altura do gr�fico
     * @param $outputPath caminho de sa�da do gr�fico
     */
    public function drawPieChart($title, $data, $width, $height, $outputPath)
    {
        // cria o modelo de dados
        $modelo = new pData;
        
        $newdata = array();
        foreach ($data as $legend => $value)
        {
            $newdata[] = $value === NULL ? VOID : ($value === 0 ? 0.0001 : $value);
            $labels[]  = $legend;
        }
        
        $modelo->addPoints($newdata, "ScoreA");
        
        /* Define the absissa serie */
        $modelo->addPoints($labels,"Labels");
        $modelo->setAbscissa("Labels");
        
        // cria o objeto que ir� conter a imagem do gr�fico
        $imagem = new pImage($width, $height, $modelo);
        
        // adiciona uma borda na forma de um ret�ngulo dentro da imagem
        $imagem->drawRectangle(0, 0, $width-1, $height-1, array("R"=>0,"G"=>0,"B"=>0));
        
        // escreve um t�tulo dentro do gr�fico
        $imagem->setFontProperties(array("FontName"=>"app/lib/pchart/fonts/Forgotte.ttf","FontSize"=>11));
        $imagem->drawText(60,35, $title, array("FontSize"=>20,"Align"=>TEXT_ALIGN_BOTTOMLEFT));
        
        // define a fonte dos dados do gr�fico
        $imagem->setFontProperties(array("FontName"=>"app/lib/pchart/fonts/Forgotte.ttf","FontSize"=>10,"R"=>80,"G"=>80,"B"=>80)); 
        
        // cria o gr�fico de torta 
        $pie = new pPie($imagem, $modelo); 
        
        // desenha o gr�fico de torta
        $pie->draw3DPie($width/2, $height/2,
                             array('WriteValues'=>TRUE, // escreve valores
                                   'DrawLabels'=>TRUE,  // escreve labels
                                   "Radius" => $width /4, // radio
                                   'ValueR'=>0, 'ValueG'=>0, 'ValueB'=>0, 'ValueAlpha' => 80));
         
        // desenha a legenda do gr�fico
        $imagem->setShadow(FALSE); 
        $pie->drawPieLegend(15, 40, array("Alpha"=>20));
        
        // grava o gr�fico em um arquivo
        $imagem->render($outputPath);
    }
}
?>
