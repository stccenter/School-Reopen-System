<!DOCTYPE html>
<?php
exec('ABM/jdk/bin/java -cp ABM/plugins/org.eclipse.equinox.launcher*.jar -Xms512m -Xmx2048m -Djava.awt.headless=true org.eclipse.core.launcher.Main -application msi.gama.headless.id4 ABM/headless/samples/roadTraffic.xml test1');
 
?>
