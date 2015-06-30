<?php
    include_once 'PageContent.php';
    include_once basename(__DIR__) . '/../Settings.php';
?>

<!DOCTYPE html>

<html>

	<head>
		<meta http-equiv="content-type" content="text/html; charset=utf-8"/>
		<title><?= $pageContent->getTitle();?></title>
		<meta name="keywords" content="Bijoux all'uncinetto"/>
		<meta name="description" content="Sito di vendita di gioielli all'uncinetto/>
		<link rel="shortcut icon" type="image/x-icon" href="gomitolo.gif"/>
		<link href="../css/Bijou.css" rel="stylesheet" type="text/css" media="screen"/>
		<link href='http://fonts.googleapis.com/css?family=Pacifico' rel='stylesheet' type='text/css'>
	</head>

	<body>
            <div id="page">
		<div id="header">
                    <div id="logo">
                                        
                        <?php
                            $header = $pageContent->getHeader();
                            require $header;
                            ?>
                                    
                    </div>
                    <div id="menu">
                                    
                        <?php
                        $menu = $pageContent->getMenu();
                        require $menu;
                        ?>
                    </div>

		</div>

		<!-- start page -->
                <!--  sidebar 1 -->
                <div id="sidebar1">
                    
                    <?php 
                    $sidebar = $pageContent->getSidebar();
                    require $sidebar;
                    ?>
                    
                </div>

                <!-- contenuto -->

		<div id="content">

                    <?php
                    $content = $pageContent->getContent();
                    require $content;
                    ?>

		</div>
                

                <div style="clear: both; width: 0px; height: 0px;"></div>
                <!--  footer -->
                <footer>
                <div id="footer">
                    <p>Applicazione per l'esame di Amministrazione di Sistema</p></br>
                    <p>Realizzata da Mattia Ibba | Matricola 44910</p>


                </div>
                <div class="validator">
                    <p>
                        <a href="http://validator.w3.org/check/referer" class="xhtml" title="Questa pagina contiene HTML valido">
                            <abbr title="eXtensible HyperText Markup Language">HTML</abbr> Valido</a>
                        <a href="http://jigsaw.w3.org/css-validator/check/referer" class="css" title="Questa pagina ha CSS validi">
                            <abbr title="Cascading Style Sheets">CSS</abbr> Valido</a>
                    </p>
                </div>
               </footer>
            </div>


	</body>
</html>
