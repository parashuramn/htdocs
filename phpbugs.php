<?php 

if(in_array($safeVarValue, $unique_fld))
	mysqli_query($link,"update scormvars set VarValue='$safeVarValue' where ((SCOInstanceID=$SCOInstanceID) and (VarName='$safeVarName'))");

	return;

}
