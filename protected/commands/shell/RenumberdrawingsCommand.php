<?php
/*
 * Deprecated - but kept for basis of similar
 */
class RenumberdrawingsCommand extends CConsoleCommand
{
   
    private $_authManager;
 
    public function getHelp()
	{
		return <<<EOD
USAGE
	renumberdrawings

DESCRIPTION
	This command renumbers drawings as per id_new column that you should add to tbl_drawing table along with id_old. This takes care of renaming the drawing
	folders.
	
	It is advised to make a full system backup first at openhost.co.nz
	- under subscriptions

EOD;
	}
	
	/**
	 * Execute the action.
	 * @param array command line parameters specific for this command
	 */
	public function run($args)
	{
		//ensure that an authManager is defined as this is mandatory for creating an auth heirarchy
		if(($this->_authManager=Yii::app()->authManager)===null)
		{
		    echo "Error: an authorization manager, named 'authManager' must be con-figured to use this command.\n";
			echo "If you already added 'authManager' component in application con-figuration,\n";
			echo "please quit and re-enter the yiic shell.\n";

			return;
		}  
		
		//provide the oportunity for the use to abort the request
		echo "Would you like to continue? [Yes|No] ";
	   
	    //check the input from the user and continue if they indicated yes to the above question
	    if(!strncasecmp(trim(fgets(STDIN)),'y',1)) 
		{
			// file path
			$path = Yii::app()->params['privateUploadPath'];
			
			// 1st phase ensuring unique by adding max(id)
			try
			{
				// start a transaction
				$transaction = Yii::app()->db->beginTransaction();

				$max = Yii::app()->db
					->createCommand("SELECT MAX(id) + MAX(id_new) AS max FROM tbl_drawing")
					->queryScalar();
				
				Yii::app()->db->createCommand("
					UPDATE `tbl_drawing` SET id_old = id;
					UPDATE `tbl_drawing` SET id = (id_new + $max);
				")->execute();
				
				echo '\nupdated ids in database - 1st pass\n';
				echo "\ntarget directory is $path\n";
				
				// rename the directories - 1st phase + max
				$cntr = 1;
				foreach(Drawing::model()->findAll() as $drawing)
				{
					exec("mv {$path}drawing/{$drawing->id_old} {$path}drawing/{$drawing->id}");
					
					echo "\n1st pass {$drawing->id_old} to {$drawing->id}";
					$cntr++;
				}

				// second phase - to our target values
				Yii::app()->db->createCommand("
					UPDATE `tbl_drawing` SET id = (id - $max);
				")->execute();

				// rename the directories - 1st phase + max
				$cntr = 1;
				foreach(Drawing::model()->findAll() as $drawing)
				{
					$old = $drawing->id_old + $max;
					exec("mv {$path}drawing/{$old} {$path}drawing/{$drawing->id}");
					
					echo "\n2nd pass {$old} to {$drawing->id}";
					$cntr++;
				}

				$transaction->commit();
			}
			catch (CDbException $e)
			{
				echo $e->getMessage();

				$transaction->rollBack();

				exit;
			}

			//provide a message indicating success
			echo "Complete. Please check all ok and if not restore the backup you hopeufully made at Openhost!";
        } 
    }
}
