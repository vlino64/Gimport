<?php
	session_start();
        require_once('../bbdd/connect.php');
        require_once('../func/constants.php');
        require_once('../func/generic.php');
        require_once('../func/seguretat.php');
	$db->exec("set names utf8");
	
	$idalumnes    = isset($_SESSION['alumne'])       ? $_SESSION['alumne']    : 0;
	$curs_escolar = isset($_SESSION['curs_escolar']) ? $_SESSION['curs_escolar'] : 0;
		
	if ($idalumnes==0 || $curs_escolar==0) {
		exit;
	}	
?>    
        <h5>Est&agrave;s matriculat de ...</h5>
        <ul id="tree_mat" class="easyui-tree" data-options="animate:true">
            <li>
                <span>&nbsp;</span>
                <ul>
                   <?php
					$grup_actual   = 0;
					
                    $rsMateries = getMateriesAlumne($db,$curs_escolar,$idalumnes);
					foreach($rsMateries->fetchAll() as $row) {
						if ($row['idgrups'] == $grup_actual) {
						}
						else {
							if ($grup_actual != 0) {
								echo "</ul>";	
								echo "</li>";
							}
							echo "<li>";
							echo "<span><strong>".$row['grup']."</strong></span>";
							echo "<ul>";
							$grup_actual = $row['idgrups'];
						}
						
						echo "<li>";
                        echo "<span>".$row['materia']."</span>";
                        echo "</li>";
						
					}
					
					if (isset($rsMateries)) {
						//mysql_free_result($rsMateries);
					}
				   ?>                    
                </ul>
            </li>
        </ul>