<?php

/*
╔══CLASS════════════════════════════════════════════════════════╗
║	== Survey Model v1.2 r0 ==				║
║	Represents a survey.					║
║	Serializable.						║
╚═══════════════════════════════════════════════════════════════╝
*/


class Survey
{

protected $Code;

protected $Name;
protected $Type;
protected $Instructions;
protected $Status;

protected $Questions;
protected $Answers;


	//--CONSTRUCTOR--
	public function __construct( $inDataArray =array() )
	{

		if( isset($inDataArray['code']) ) 		{ $this->Code = $inDataArray['code']; }

		if( isset($inDataArray['name']) ) 		{ $this->Name = $inDataArray['name']; }
		if( isset($inDataArray['type']) ) 		{ $this->Type = $inDataArray['type']; }
		if( isset($inDataArray['instructions']) ) 	{ $this->Instructions = $inDataArray['instructions']; }
		if( isset($inDataArray['status']) ) 		{ $this->Status = $inDataArray['status']; }

		if( isset($inDataArray['questions']) ) 		{ $this->Questions = $inDataArray['questions']; }
		if( isset($inDataArray['answers']) ) 		{ $this->Answers = $inDataArray['answers']; }
	}


	//--GET FUNCTIONS--
	public function GetCode() { return $this->Code; }

	public function GetName() { return $this->Name; }
	public function GetType() { return $this->Type; }
	public function GetInstructions() { return $this->Instructions; }
	public function GetStatus() { return $this->Status; }

	public function GetQuestions() { return $this->Questions; }
	public function GetAnswers() { return $this->Answers; }


	//--SET FUNCTIONS--
	public function SetCode($inCode) { $this->Code = $inCode; }

	public function SetName($inName) { $this->Name = $inName; }
	public function SetType($inType) { $this->Type = $inType; }
	public function SetInstructions($inText) { $this->Instructions = $inText; }
	public function SetStatus($inStatus) { $this->Status = $inStatus; }

	public function SetQuestions($inList) { $this->Questions = $inList; }
	public function SetAnswers($inList) { $this->Answers = $inList; }


	//--GET LIST COUNTS--
	public function GetQuestionCount() { return count($this->Questions); }
	public function GetAnswerCount() { return count($this->Answers); }


	//--OTHERS--


	//--PRINT OBJECT FOR DEBUG PURPOSES--
	public function PrintHTML()
	{
		echo '<DIV class="debug">';
		echo '<b><u>Survey</u></b><br />';
		echo 'Code: ' . $this->Code . '<br />';
		echo 'Type: ' . $this->Type . '<br />';
		echo 'Status: ' . $this->Status . '<br />';
		echo '-------<br />';
		echo 'Questions: ' . count($this->Questions) . '<br />';
		echo 'Answers: ' . count($this->Answers) . '<br />';
		echo '</DIV>';
	}


} // END of Survey class

?>
