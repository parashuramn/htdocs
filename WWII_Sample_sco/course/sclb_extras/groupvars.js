/**
 *  ==================================================
 *  SoftChalk LessonBuilder q_parameters.js
 *  Copyright 2003-2010 SoftChalk LLC
 *  All Rights Reserved.
 *
 *  http://www.softchalk.com
 *  ==================================================
 */

var groupFinished = new Array();
var currentQPNum = new Array();
var quizGroupSize = new Array();
var firstDisplayed = new Array();
var quizGroupToggle_array = new Array();

function randomizeQp(myArray) {
var i = myArray.length;
  while ( --i ) {
    var j = Math.floor(Math.random() * (i + 1));
    var tempi = myArray[i];
    var tempj = myArray[j];
    myArray[i] = tempj;
    myArray[j] = tempi;
  }
}


groupFinished[1] = false;
firstDisplayed[1] = false;
currentQPNum[1] = 1;
var groupQpArray1 = new Array();
var groupQpStateArray1 = new Array();
var qpNumbersArray1 = new Array();
qpNumbersArray1[0] = 1;
qpNumbersArray1[1] = 2;
qpNumbersArray1[2] = 3;
qpNumbersArray1[3] = 4;
qpNumbersArray1[4] = 5;
quizGroupSize[1] = 5;
groupQpArray1[1]='<img src="spacer.gif" name="check1" alt=""><!-- qpstart --><div id="quizpopper1" class="expand"><div style="padding: 5px 10px;">Value: 1</div><div class="qpq" style="border: 1px solid #A9390B; background: #FCD3C4; line-height: 1.5em; padding: 10px 15px; width: 430px;"><form name="f1">World War II in Europe began on September 1, 1939, when</p><p>&nbsp;<div style="margin: 1em 15px;"><input type="radio" name="q1" value="a" id="q1a">&nbsp;<label for="q1a"><strong>a.</strong>&nbsp;Great Britain and France declared war on Germany and Austria over the seizure of Czech&#173;oslovakia.</label><br><input type="radio" name="q1" value="b" id="q1b">&nbsp;<label for="q1b"><strong>b.</strong>&nbsp;Russia invaded Poland.</label><br><input type="radio" name="q1" value="c" id="q1c">&nbsp;<label for="q1c"><strong>c.</strong>&nbsp;Italy attacked Crete.</label><br><input type="radio" name="q1" value="d" id="q1d">&nbsp;<label for="q1d"><strong>d.</strong>&nbsp;Germany invaded Poland.&nbsp;</label><br></div><div align="center"></div></form><div class="collapse" id="f_done1" style="margin: 1em;"></div><div class="collapse" id="feed1" style="font-family: Comic Sans MS; border-top: 1px solid #A9390B; margin: 1em;"></div></div></div><!-- qpend -->';
groupQpArray1[2]='<img src="spacer.gif" name="check2" alt=""><!-- qpstart --><div id="quizpopper2" class="expand"><div style="padding: 5px 10px;">Value: 1</div><div class="qpq" style="border: 1px solid #A9390B; background: #FCD3C4; line-height: 1.5em; padding: 10px 15px; width: 430px;"><form name="f2">The first ground offensive action taken by the United States in world War II was<div style="margin: 1em 15px;"><input type="radio" name="q2" value="a" id="q2a">&nbsp;<label for="q2a"><strong>a.</strong>&nbsp;the Marine landing on Guadalcanal</label><br><input type="radio" name="q2" value="b" id="q2b">&nbsp;<label for="q2b"><strong>b.</strong>&nbsp;the invasion of Normandy in France</label><br><input type="radio" name="q2" value="c" id="q2c">&nbsp;<label for="q2c"><strong>c.</strong>&nbsp;the landing of American troops in Iceland</label><br><input type="radio" name="q2" value="d" id="q2d">&nbsp;<label for="q2d"><strong>d.</strong>&nbsp;the American landing on the island of Sicily</label><br></div><div align="center"></div></form><div class="collapse" id="f_done2" style="margin: 1em;"></div><div class="collapse" id="feed2" style="font-family: Comic Sans MS; border-top: 1px solid #A9390B; margin: 1em;"></div></div></div><!-- qpend -->';
groupQpArray1[3]='<img src="spacer.gif" name="check3" alt=""><!-- qpstart --><div id="quizpopper3" class="expand"><div style="padding: 5px 10px;">Value: 1</div><div class="qpq" style="border: 1px solid #A9390B; background: #FCD3C4; line-height: 1.5em; padding: 10px 15px; width: 430px;"><form name="f3">Causes of World War 2 include<div style="margin: 1em 15px;"><input type="checkbox" name="q3" value="a" id="q3a">&nbsp;<label for="q3a"><strong>a.</strong>&nbsp;the German invasion of Poland</label><br><input type="checkbox" name="q3" value="b" id="q3b">&nbsp;<label for="q3b"><strong>b.</strong>&nbsp;the Italian invasion of Ethiopia</label><br><input type="checkbox" name="q3" value="c" id="q3c">&nbsp;<label for="q3c"><strong>c.</strong>&nbsp;the Spanish Civil War</label><br><input type="checkbox" name="q3" value="d" id="q3d">&nbsp;<label for="q3d"><strong>d.</strong>&nbsp;Japanese aggression in Asia</label><br><br><span style="font-size: 90%;">[mark all correct answers]</span></div><div align="center"></div></form><div class="collapse" id="f_done3" style="margin: 1em;"></div><div class="collapse" id="feed3" style="font-family: Comic Sans MS; border-top: 1px solid #A9390B; margin: 1em;"></div></div></div><!-- qpend -->';
groupQpArray1[4]='<img src="spacer.gif" name="check4" alt=""><!-- qpstart --><div id="quizpopper4" class="expand"><div style="padding: 5px 10px;">Value: 1</div><div class="qpq" style="border: 1px solid #A9390B; background: #FCD3C4; line-height: 1.5em; padding: 10px 15px; width: 430px;"><form name="f4">Major turning points in World War II include<div style="margin: 1em 15px;"><input type="checkbox" name="q4" value="a" id="q4a">&nbsp;<label for="q4a"><strong>a.</strong>&nbsp;the German invasion of Great Britain</label><br><input type="checkbox" name="q4" value="b" id="q4b">&nbsp;<label for="q4b"><strong>b.</strong>&nbsp;the German defeat at Stalingrad</label><br><input type="checkbox" name="q4" value="c" id="q4c">&nbsp;<label for="q4c"><strong>c.</strong>&nbsp;the Battle of Midway</label><br><input type="checkbox" name="q4" value="d" id="q4d">&nbsp;<label for="q4d"><strong>d.</strong>&nbsp;the Japanese capture of Attu and Kiska</label><br><br><span style="font-size: 90%;">[mark all correct answers]</span></div><div align="center"></div></form><div class="collapse" id="f_done4" style="margin: 1em;"></div><div class="collapse" id="feed4" style="font-family: Comic Sans MS; border-top: 1px solid #A9390B; margin: 1em;"></div></div></div><!-- qpend -->';
groupQpArray1[5]='<img src="spacer.gif" name="check5" alt=""><!-- qpstart --><div id="quizpopper5" class="expand"><div style="padding: 5px 10px;">Value: 1</div><div class="qpq" style="border: 1px solid #A9390B; background: #FCD3C4; line-height: 1.5em; padding: 10px 15px; width: 430px;"><form name="f5">Immediately after Pearl Harbor American and British strategists decided to<div style="margin: 1em 15px;"><input type="radio" name="q5" value="a" id="q5a">&nbsp;<label for="q5a"><strong>a.</strong>&nbsp;concentrate first against Japan.</label><br><input type="radio" name="q5" value="b" id="q5b">&nbsp;<label for="q5b"><strong>b.</strong>&nbsp;develop the atomic bomb.</label><br><input type="radio" name="q5" value="c" id="q5c">&nbsp;<label for="q5c"><strong>c.</strong>&nbsp;concentrate first against Germany.</label><br><input type="radio" name="q5" value="d" id="q5d">&nbsp;<label for="q5d"><strong>d.</strong>&nbsp;develop radar.</p><p>&nbsp;</label><br></div><div align="center"></div></form><div class="collapse" id="f_done5" style="margin: 1em;"></div><div class="collapse" id="feed5" style="font-family: Comic Sans MS; border-top: 1px solid #A9390B; margin: 1em;"></div></div></div><!-- qpend -->';

