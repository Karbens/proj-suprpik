<?php 
 
    /*----------------------------------------------------------------------
        Table Extractor
        ===============
        Table extractor is a php class that can extract almost any table
        from any html document/page, and then convert that html table into
        a php array.
        
        Version 1.3
        Compatibility: PHP 4.4.1 +
        Copyright Jack Sleight - www.reallyshiny.com
        This script is licensed under the Creative Commons License.
    ----------------------------------------------------------------------*/
 
    class tableExtractor {
    
        var $source            = NULL;
        var $anchor            = NULL;
        var $anchorWithin    = false;
        var $headerRow        = true;
        var $startRow        = 0;
        var $maxRows        = 0;
        var $startCol        = 0;
        var $maxCols        = 0;
        var $stripTags        = false;
        var $extraCols        = array();
        var $rowCount        = 0;
        var $dropRows        = NULL;
        
        var $cleanHTML        = NULL;
        var $rawArray        = NULL;
        var $finalArray        = NULL;
        
        function extractTable() {
        
            $this->cleanHTML();
            $this->prepareArray();
            
            return $this->createArray();
            
        }
    
 
        function cleanHTML() {
        
            // php 4 compatibility functions
            if(!function_exists('stripos')) {
                function stripos($haystack,$needle,$offset = 0) {
                   return(strpos(strtolower($haystack),strtolower($needle),$offset));
                }
            }
                        
            // find unique string that appears before the table you want to extract
            if ($this->anchorWithin) {
                /*------------------------------------------------------------
                    With thanks to Khary Sharp for suggesting and writing
                    the anchor within functionality.
                ------------------------------------------------------------*/                
                $anchorPos = stripos($this->source, $this->anchor) + strlen($this->anchor);
                $sourceSnippet = strrev(substr($this->source, 0, $anchorPos));
                $tablePos = stripos($sourceSnippet, strrev(("<table"))) + 6;
                $startSearch = strlen($sourceSnippet) - $tablePos;
            }                       
            else {
                $startSearch = stripos($this->source, $this->anchor);
            }
        
            // extract table
            $startTable = stripos($this->source, '<table', $startSearch);
            $endTable = stripos($this->source, '</table>', $startTable) + 8;
            $table = substr($this->source, $startTable, $endTable - $startTable);
        
            if(!function_exists('lcase_tags')) {
                function lcase_tags($input) {
                    return strtolower($input[0]);
                }
            }
            
            // lowercase all table related tags
            $table = preg_replace_callback('/<(\/?)(table|tr|th|td)/is', 'lcase_tags', $table);
            
            // remove all thead and tbody tags
            $table = preg_replace('/<\/?(thead|tbody).*?>/is', '', $table);
            
            // replace th tags with td tags
            $table = preg_replace('/<(\/?)th(.*?)>/is', '<$1td$2>', $table);
                                    
            // clean string
            $table = trim($table);
            $table = str_replace("\r\n", "", $table); 
                            
            $this->cleanHTML = $table;
        
        }
        
        function prepareArray() {
        
            // split table into individual elements
            $pattern = '/(<\/?(?:tr|td).*?>)/is';
            $table = preg_split($pattern, $this->cleanHTML, -1, PREG_SPLIT_DELIM_CAPTURE);    
 
            // define array for new table
            $tableCleaned = array();
            
            // define variables for looping through table
            $rowCount = 0;
            $colCount = 1;
            $trOpen = false;
            $tdOpen = false;
            
            // loop through table
            foreach($table as $item) {
            
                // trim item
                $item = str_replace(' ', '', $item);
                $item = trim($item);
                
                // save the item
                $itemUnedited = $item;
                
                // clean if tag                                    
                $item = preg_replace('/<(\/?)(table|tr|td).*?>/is', '<$1$2>', $item);
 
                // pick item type
                switch ($item) {
                    
 
                    case '<tr>':
                        // start a new row
                        $rowCount++;
                        $colCount = 1;
                        $trOpen = true;
                        break;
                        
                    case '<td>':
                        // save the td tag for later use
                        $tdTag = $itemUnedited;
                        $tdOpen = true;
                        break;
                        
                    case '</td>':
                        $tdOpen = false;
                        break;
                        
                    case '</tr>':
                        $trOpen = false;
                        break;
                        
                    default :
                    
                        // if a TD tag is open
                        if($tdOpen) {
                        
                            // check if td tag contained colspan                                            
                            if(preg_match('/<td [^>]*colspan\s*=\s*(?:\'|")?\s*([0-9]+)[^>]*>/is', $tdTag, $matches))
                                $colspan = $matches[1];
                            else
                                $colspan = 1;
                                                    
                            // check if td tag contained rowspan
                            if(preg_match('/<td [^>]*rowspan\s*=\s*(?:\'|")?\s*([0-9]+)[^>]*>/is', $tdTag, $matches))
                                $rowspan = $matches[1];
                            else
                                $rowspan = 0;
                                
                            // loop over the colspans
                            for($c = 0; $c < $colspan; $c++) {
                                                    
                                // if the item data has not already been defined by a rowspan loop, set it
                                if(!isset($tableCleaned[$rowCount][$colCount]))
                                    $tableCleaned[$rowCount][$colCount] = $item;
                                else
                                    $tableCleaned[$rowCount][$colCount + 1] = $item;
                                    
                                // create new rowCount variable for looping through rowspans
                                $futureRows = $rowCount;
                                
                                // loop through row spans
                                for($r = 1; $r < $rowspan; $r++) {
                                    $futureRows++;                                    
                                    if($colspan > 1)
                                        $tableCleaned[$futureRows][$colCount + 1] = $item;
                                    else                    
                                        $tableCleaned[$futureRows][$colCount] = $item;
                                }
    
                                // increase column count
                                $colCount++;
                            
                            }
                            
                            // sort the row array by the column keys (as inserting rowspans screws up the order)
                            ksort($tableCleaned[$rowCount]);
                        }
                        break;
                }    
            }
            // set row count
            if($this->headerRow)
                $this->rowCount    = count($tableCleaned) - 1;
            else
                $this->rowCount    = count($tableCleaned);
            
            $this->rawArray = $tableCleaned;
            
        }
        
        function createArray() {
            
            // define array to store table data
            $tableData = array();
            
            // get column headers
            if($this->headerRow) {
            
                // trim string
                $row = $this->rawArray[$this->headerRow];
                            
                // set column names array
                $columnNames = array();
                $uniqueNames = array();
                        
                // loop over column names
                $colCount = 0;
                foreach($row as $cell) {
                                
                    $colCount++;
                    
                    $cell = strip_tags($cell);
                    $cell = trim($cell);
                    
                    // save name if there is one, otherwise save index
                    if($cell) {
                    
                        if(isset($uniqueNames[$cell])) {
                            $uniqueNames[$cell]++;
                            $cell .= ' ('.($uniqueNames[$cell] + 1).')';    
                        }            
                        else {
                            $uniqueNames[$cell] = 0;
                        }
 
                        $columnNames[$colCount] = $cell;
                        
                    }                        
                    else
                        $columnNames[$colCount] = $colCount;
                    
                }
                
                // remove the headers row from the table
                unset($this->rawArray[$this->headerRow]);
    
            }
            
            // remove rows to drop
            foreach(explode(',', $this->dropRows) as $key => $value) {
                unset($this->rawArray[$value]);
            }
                                
            // set the end row
            if($this->maxRows)
                $endRow = $this->startRow + $this->maxRows - 1;
            else
                $endRow = count($this->rawArray);
                
            // loop over row array
            $rowCount = 0;
            $newRowCount = 0;                            
            foreach($this->rawArray as $row) {
            
                $rowCount++;
                
                // if the row was requested then add it
                if($rowCount >= $this->startRow && $rowCount <= $endRow) {
                
                    $newRowCount++;
                                    
                    // create new array to store data
                    $tableData[$newRowCount] = array();
                    
                    //$tableData[$newRowCount]['origRow'] = $rowCount;
                    //$tableData[$newRowCount]['data'] = array();
                    $tableData[$newRowCount] = array();
                    
                    // set the end column
                    if($this->maxCols)
                        $endCol = $this->startCol + $this->maxCols - 1;
                    else
                        $endCol = count($row);
                    
                    // loop over cell array
                    $colCount = 0;
                    $newColCount = 0;                                
                    foreach($row as $cell) {
                    
                        $colCount++;
                        
                        // if the column was requested then add it
                        if($colCount >= $this->startCol && $colCount <= $endCol) {
                    
                            $newColCount++;
                            
                            if($this->extraCols) {
                                foreach($this->extraCols as $extraColumn) {
                                    if($extraColumn['column'] == $colCount) {
                                        if(preg_match($extraColumn['regex'], $cell, $matches)) {
                                            if(is_array($extraColumn['names'])) {
                                                $this->extraColsCount = 0;
                                                foreach($extraColumn['names'] as $extraColumnSub) {
                                                    $this->extraColsCount++;
                                                    $tableData[$newRowCount][$extraColumnSub] = $matches[$this->extraColsCount];
                                                }                                        
                                            } else {
                                                $tableData[$newRowCount][$extraColumn['names']] = $matches[1];
                                            }
                                        } else {
                                            $this->extraColsCount = 0;
                                            if(is_array($extraColumn['names'])) {
                                                $this->extraColsCount = 0;
                                                foreach($extraColumn['names'] as $extraColumnSub) {
                                                    $this->extraColsCount++;
                                                    $tableData[$newRowCount][$extraColumnSub] = '';
                                                }                                        
                                            } else {
                                                $tableData[$newRowCount][$extraColumn['names']] = '';
                                            }
                                        }
                                    }
                                }
                            }
                            
                            if($this->stripTags)        
                                $cell = strip_tags($cell);
                            
                            // set the column key as the column number
                            $colKey = $newColCount;
                            
                            // if there is a table header, use the column name as the key
                            if($this->headerRow)
                                if(isset($columnNames[$colCount]))
                                    $colKey = $columnNames[$colCount];
                            
                            // add the data to the array
                            //$tableData[$newRowCount]['data'][$colKey] = $cell;
                            $tableData[$newRowCount][$colKey] = $cell;
                        }
                    }
                }
            }
                    
            $this->finalArray = $tableData;
            return $tableData;
        }    
    }
$b_data = '<table class="border" border=0 cellspacing=0 cellpadding=0 id="wagerTable">
	<tbody id="betOdds">
			
			<tr>
			<th>Date</th>
			<th>Num</th>
			<th>Team</th>
			<th>Money</th>
			<th>Blank_1</th>
			<th>Line</th>
			<th>Blank_2</th>
			<th>OverUnder</th>
			<th>Blank_3</th>
			</tr>
			
			<tr valign=middle class="odd">
            	<td class="left">
	            	06/14/11 
            	</td>
            	
				<td align="center" width="30">951</td>
				<td class="left"><a title="Team Statistics" href="#" onclick="javascript:window.open(\'http://www.sportsbook.com/statfeed/betting/statfeed.php?page=mlb/mlbteam&teamid=ST LOUIS\', \'statfox\'); return false;">Cardinals(StLouis)<br>Garcia</a></td>

				<td align="right"><a title="Click to bet on Cardinals(StLouis) to win straight up." target="TICKET" href="#" onclick="javascript:quickbet(getValueFromChkBox(\'selection[Base-Cardi-Natio-Gar-May-061411MLA]\'),204, 1);return false; "><span id="Base-Cardi-Natio-Gar-May-061411MLA">-135</span></a></td>

				<td ><input type="checkbox" name="selection[Base-Cardi-Natio-Gar-May-061411MLA]" id="selection[Base-Cardi-Natio-Gar-May-061411MLA]" value="Base-Cardi-Natio-Gar-May-061411|MLA|1|100|135|0|-135" onclick="checkMoney(this.name)"></td>


				<td align="right"><a title="Click to bet on Cardinals(StLouis) to cover the spread." target="TICKET" href="#" onclick="javascript:quickbet(getValueFromChkBox(\'selection[Base-Cardi-Natio-Gar-May-061411PSA]\'),204, 1);return false; "><span id="Base-Cardi-Natio-Gar-May-061411PSA">-1.5 (+125)</span></a></td>

				<td ><input type="checkbox" name="selection[Base-Cardi-Natio-Gar-May-061411PSA]" id="selection[Base-Cardi-Natio-Gar-May-061411PSA]" value="Base-Cardi-Natio-Gar-May-061411|PSA|1|125|100|-3|+125" onclick="checkPoint(this.name)"></td>


				<td class="overUnder" align="right"><a title="Click to bet on the games total score to be over 7" target="TICKET" href="#" onclick="javascript:quickbet(getValueFromChkBox(\'selection[Base-Cardi-Natio-Gar-May-061411TLO]\'),204, 1);return false; "><span id="Base-Cardi-Natio-Gar-May-061411TLO">Over 7.5 (-115)</span></a></td>

				<td class="overUnder"><input type="checkbox" name="selection[Base-Cardi-Natio-Gar-May-061411TLO]" id="selection[Base-Cardi-Natio-Gar-May-061411TLO]" value="Base-Cardi-Natio-Gar-May-061411|TLO|1|100|115|15|-115" onclick="checkPoint(this.name)"></td>


           </tr>

			<!-- Pending / Previous row -->
			

 			

			
				<tr align="center" class="odd">
					<td class="left"><a href="#" onclick="javascript:window.open(\'http://www.sportsbook.com/statfeed/betting/statfeed.php?page=mlb/expanded&gameid=20110614WASHINGTON\',\'statfox\'); return false;" >Matchup</a></td>

					
 					<td class="left"><a href="#" onclick="javascript:window.open(\'http://www.sportsbook.com/statfeed/betting/statfeed.php?page=mlb/trend&gameid=20110614WASHINGTON\',\'statfox\'); return false;" >Trend Sheet</a></td> 
							
					<!--  Teamname and lines -->
					<td class="left"></td>
					<td></td> <td></td> 	<td></td><td></td> 	 <td class="overUnder"></td><td class="overUnder"></td>
				</tr>
			

			<tr valign=middle class="odd">
				<td class="left">

				19:05 ET 
				</td>
				
				<td align="center" width="30">952</td>
				<td class="left"><a title="Team Statistics" href="#" onclick="javascript:window.open(\'http://www.sportsbook.com/statfeed/betting/statfeed.php?page=mlb/mlbteam&teamid=WASHINGTON\', \'statfox\'); return false;">Nationals(Washington)<br>Maya</a></td>

				<td align="right"><a title="Click to bet on Nationals(Washington) to win straight up." target="TICKET" href="#" onclick="javascript:quickbet(getValueFromChkBox(\'selection[Base-Cardi-Natio-Gar-May-061411MLH]\'),204, 1);return false; "><span id="Base-Cardi-Natio-Gar-May-061411MLH">+125</span></a></td>

				<td ><input type="checkbox" name="selection[Base-Cardi-Natio-Gar-May-061411MLH]" id="selection[Base-Cardi-Natio-Gar-May-061411MLH]" value="Base-Cardi-Natio-Gar-May-061411|MLH|1|125|100|0|+125" onclick="checkMoney(this.name)"></td>


				<td align="right"><a title="Click to bet on Nationals(Washington) to cover the spread." target="TICKET" href="#" onclick="javascript:quickbet(getValueFromChkBox(\'selection[Base-Cardi-Natio-Gar-May-061411PSH]\'),204, 1);return false; "><span id="Base-Cardi-Natio-Gar-May-061411PSH">+1.5 (-145)</span></a></td>

				<td ><input type="checkbox" name="selection[Base-Cardi-Natio-Gar-May-061411PSH]" id="selection[Base-Cardi-Natio-Gar-May-061411PSH]" value="Base-Cardi-Natio-Gar-May-061411|PSH|1|100|145|3|-145" onclick="checkPoint(this.name)"></td>


				<td class="overUnder" align="right"><a title="Click to bet on the games total score to be under 7" target="TICKET" href="#" onclick="javascript:quickbet(getValueFromChkBox(\'selection[Base-Cardi-Natio-Gar-May-061411TLU]\'),204, 1);return false; "><span id="Base-Cardi-Natio-Gar-May-061411TLU">Under 7.5 (-105)</span></a></td>

				<td class="overUnder"><input type="checkbox" name="selection[Base-Cardi-Natio-Gar-May-061411TLU]" id="selection[Base-Cardi-Natio-Gar-May-061411TLU]" value="Base-Cardi-Natio-Gar-May-061411|TLU|1|100|105|15|-105" onclick="checkPoint(this.name)"></td>

			</tr>

			<!-- Pending / Previous row -->
			

			<tr><td class="trLine" colspan=9></td></tr>

		
			
			
			
			
			

			<input name="events[Base-Marli-Phill-Vol-Ham-061411]" id="events[Base-Marli-Phill-Vol-Ham-061411]" type="hidden" value="Base-Marli-Phill-Vol-Ham-061411^Phillies(Philadelphia)^Marlins(Florida)^06-14-11 7:05 PM^06/14/11^7:05 PM^204" />
			

			<tr valign=middle class="even">
            	<td class="left">
	            	06/14/11 
            	</td>

            	
				<td align="center" width="30">953</td>
				<td class="left"><a title="Team Statistics" href="#" onclick="javascript:window.open(\'http://www.sportsbook.com/statfeed/betting/statfeed.php?page=mlb/mlbteam&teamid=FLORIDA\', \'statfox\'); return false;">Marlins(Florida)<br>Volstad</a></td>

				<td align="right"><a title="Click to bet on Marlins(Florida) to win straight up." target="TICKET" href="#" onclick="javascript:quickbet(getValueFromChkBox(\'selection[Base-Marli-Phill-Vol-Ham-061411MLA]\'),204, 1);return false; "><span id="Base-Marli-Phill-Vol-Ham-061411MLA">+190</span></a></td>

				<td ><input type="checkbox" name="selection[Base-Marli-Phill-Vol-Ham-061411MLA]" id="selection[Base-Marli-Phill-Vol-Ham-061411MLA]" value="Base-Marli-Phill-Vol-Ham-061411|MLA|1|190|100|0|+190" onclick="checkMoney(this.name)"></td>


				<td align="right"><a title="Click to bet on Marlins(Florida) to cover the spread." target="TICKET" href="#" onclick="javascript:quickbet(getValueFromChkBox(\'selection[Base-Marli-Phill-Vol-Ham-061411PSA]\'),204, 1);return false; "><span id="Base-Marli-Phill-Vol-Ham-061411PSA">+1.5 (-125)</span></a></td>

				<td ><input type="checkbox" name="selection[Base-Marli-Phill-Vol-Ham-061411PSA]" id="selection[Base-Marli-Phill-Vol-Ham-061411PSA]" value="Base-Marli-Phill-Vol-Ham-061411|PSA|1|100|125|3|-125" onclick="checkPoint(this.name)"></td>


				<td class="overUnder" align="right"><a title="Click to bet on the games total score to be over 8" target="TICKET" href="#" onclick="javascript:quickbet(getValueFromChkBox(\'selection[Base-Marli-Phill-Vol-Ham-061411TLO]\'),204, 1);return false; "><span id="Base-Marli-Phill-Vol-Ham-061411TLO">Over 8 (even)</span></a></td>

				<td class="overUnder"><input type="checkbox" name="selection[Base-Marli-Phill-Vol-Ham-061411TLO]" id="selection[Base-Marli-Phill-Vol-Ham-061411TLO]" value="Base-Marli-Phill-Vol-Ham-061411|TLO|1|100|100|16|even" onclick="checkPoint(this.name)"></td>


           </tr>

			<!-- Pending / Previous row -->

			

 			

			
				<tr align="center" class="even">
					<td class="left"><a href="#" onclick="javascript:window.open(\'http://www.sportsbook.com/statfeed/betting/statfeed.php?page=mlb/expanded&gameid=20110614PHILADELPHIA\',\'statfox\'); return false;" >Matchup</a></td>
					
 					<td class="left"><a href="#" onclick="javascript:window.open(\'http://www.sportsbook.com/statfeed/betting/statfeed.php?page=mlb/trend&gameid=20110614PHILADELPHIA\',\'statfox\'); return false;" >Trend Sheet</a></td> 
							
					<!--  Teamname and lines -->
					<td class="left"></td>
					<td></td> <td></td> 	<td></td><td></td> 	 <td class="overUnder"></td><td class="overUnder"></td>
				</tr>

			

			<tr valign=middle class="even">
				<td class="left">
				19:05 ET 
				</td>
				
				<td align="center" width="30">954</td>
				<td class="left"><a title="Team Statistics" href="#" onclick="javascript:window.open(\'http://www.sportsbook.com/statfeed/betting/statfeed.php?page=mlb/mlbteam&teamid=PHILADELPHIA\', \'statfox\'); return false;">Phillies(Philadelphia)<br>Hamels</a></td>

				<td align="right"><a title="Click to bet on Phillies(Philadelphia) to win straight up." target="TICKET" href="#" onclick="javascript:quickbet(getValueFromChkBox(\'selection[Base-Marli-Phill-Vol-Ham-061411MLH]\'),204, 1);return false; "><span id="Base-Marli-Phill-Vol-Ham-061411MLH">-210</span></a></td>

				<td ><input type="checkbox" name="selection[Base-Marli-Phill-Vol-Ham-061411MLH]" id="selection[Base-Marli-Phill-Vol-Ham-061411MLH]" value="Base-Marli-Phill-Vol-Ham-061411|MLH|1|100|210|0|-210" onclick="checkMoney(this.name)"></td>


				<td align="right"><a title="Click to bet on Phillies(Philadelphia) to cover the spread." target="TICKET" href="#" onclick="javascript:quickbet(getValueFromChkBox(\'selection[Base-Marli-Phill-Vol-Ham-061411PSH]\'),204, 1);return false; "><span id="Base-Marli-Phill-Vol-Ham-061411PSH">-1.5 (+105)</span></a></td>

				<td ><input type="checkbox" name="selection[Base-Marli-Phill-Vol-Ham-061411PSH]" id="selection[Base-Marli-Phill-Vol-Ham-061411PSH]" value="Base-Marli-Phill-Vol-Ham-061411|PSH|1|105|100|-3|+105" onclick="checkPoint(this.name)"></td>


				<td class="overUnder" align="right"><a title="Click to bet on the games total score to be under 8" target="TICKET" href="#" onclick="javascript:quickbet(getValueFromChkBox(\'selection[Base-Marli-Phill-Vol-Ham-061411TLU]\'),204, 1);return false; "><span id="Base-Marli-Phill-Vol-Ham-061411TLU">Under 8 (-120)</span></a></td>

				<td class="overUnder"><input type="checkbox" name="selection[Base-Marli-Phill-Vol-Ham-061411TLU]" id="selection[Base-Marli-Phill-Vol-Ham-061411TLU]" value="Base-Marli-Phill-Vol-Ham-061411|TLU|1|100|120|16|-120" onclick="checkPoint(this.name)"></td>


			</tr>

			<!-- Pending / Previous row -->
			

			<tr><td class="trLine" colspan=9></td></tr>

		
			
			
			
			
			

			<input name="events[Base-Range-Yanke-Oga-Sab-061411]" id="events[Base-Range-Yanke-Oga-Sab-061411]" type="hidden" value="Base-Range-Yanke-Oga-Sab-061411^Yankees(NewYork)^Rangers(Texas)^06-14-11 7:05 PM^06/14/11^7:05 PM^204" />
			

			<tr valign=middle class="odd">
            	<td class="left">

	            	06/14/11 
            	</td>
            	
				<td align="center" width="30">967</td>
				<td class="left"><a title="Team Statistics" href="#" onclick="javascript:window.open(\'http://www.sportsbook.com/statfeed/betting/statfeed.php?page=mlb/mlbteam&teamid=TEXAS\', \'statfox\'); return false;">Rangers(Texas)<br>Ogando</a></td>

				<td align="right"><a title="Click to bet on Rangers(Texas) to win straight up." target="TICKET" href="#" onclick="javascript:quickbet(getValueFromChkBox(\'selection[Base-Range-Yanke-Oga-Sab-061411MLA]\'),204, 1);return false; "><span id="Base-Range-Yanke-Oga-Sab-061411MLA">+130</span></a></td>

				<td ><input type="checkbox" name="selection[Base-Range-Yanke-Oga-Sab-061411MLA]" id="selection[Base-Range-Yanke-Oga-Sab-061411MLA]" value="Base-Range-Yanke-Oga-Sab-061411|MLA|1|130|100|0|+130" onclick="checkMoney(this.name)"></td>


				<td align="right"><a title="Click to bet on Rangers(Texas) to cover the spread." target="TICKET" href="#" onclick="javascript:quickbet(getValueFromChkBox(\'selection[Base-Range-Yanke-Oga-Sab-061411PSA]\'),204, 1);return false; "><span id="Base-Range-Yanke-Oga-Sab-061411PSA">+1.5 (-165)</span></a></td>

				<td ><input type="checkbox" name="selection[Base-Range-Yanke-Oga-Sab-061411PSA]" id="selection[Base-Range-Yanke-Oga-Sab-061411PSA]" value="Base-Range-Yanke-Oga-Sab-061411|PSA|1|100|165|3|-165" onclick="checkPoint(this.name)"></td>


				<td class="overUnder" align="right"><a title="Click to bet on the games total score to be over 8" target="TICKET" href="#" onclick="javascript:quickbet(getValueFromChkBox(\'selection[Base-Range-Yanke-Oga-Sab-061411TLO]\'),204, 1);return false; "><span id="Base-Range-Yanke-Oga-Sab-061411TLO">Over 8 (even)</span></a></td>

				<td class="overUnder"><input type="checkbox" name="selection[Base-Range-Yanke-Oga-Sab-061411TLO]" id="selection[Base-Range-Yanke-Oga-Sab-061411TLO]" value="Base-Range-Yanke-Oga-Sab-061411|TLO|1|100|100|16|even" onclick="checkPoint(this.name)"></td>

           </tr>

			<!-- Pending / Previous row -->
			

 			

			
				<tr align="center" class="odd">
					<td class="left"><a href="#" onclick="javascript:window.open(\'http://www.sportsbook.com/statfeed/betting/statfeed.php?page=mlb/expanded&gameid=20110614NYYANKEES\',\'statfox\'); return false;" >Matchup</a></td>
					
 					<td class="left"><a href="#" onclick="javascript:window.open(\'http://www.sportsbook.com/statfeed/betting/statfeed.php?page=mlb/trend&gameid=20110614NYYANKEES\',\'statfox\'); return false;" >Trend Sheet</a></td> 
							
					<!--  Teamname and lines -->
					<td class="left"></td>

					<td></td> <td></td> 	<td></td><td></td> 	 <td class="overUnder"></td><td class="overUnder"></td>
				</tr>
			

			<tr valign=middle class="odd">
				<td class="left">
				19:05 ET 
				</td>
				
				<td align="center" width="30">968</td>

				<td class="left"><a title="Team Statistics" href="#" onclick="javascript:window.open(\'http://www.sportsbook.com/statfeed/betting/statfeed.php?page=mlb/mlbteam&teamid=NY YANKEES\', \'statfox\'); return false;">Yankees(NewYork)<br>Sabathia</a></td>

				<td align="right"><a title="Click to bet on Yankees(NewYork) to win straight up." target="TICKET" href="#" onclick="javascript:quickbet(getValueFromChkBox(\'selection[Base-Range-Yanke-Oga-Sab-061411MLH]\'),204, 1);return false; "><span id="Base-Range-Yanke-Oga-Sab-061411MLH">-140</span></a></td>

				<td ><input type="checkbox" name="selection[Base-Range-Yanke-Oga-Sab-061411MLH]" id="selection[Base-Range-Yanke-Oga-Sab-061411MLH]" value="Base-Range-Yanke-Oga-Sab-061411|MLH|1|100|140|0|-140" onclick="checkMoney(this.name)"></td>


				<td align="right"><a title="Click to bet on Yankees(NewYork) to cover the spread." target="TICKET" href="#" onclick="javascript:quickbet(getValueFromChkBox(\'selection[Base-Range-Yanke-Oga-Sab-061411PSH]\'),204, 1);return false; "><span id="Base-Range-Yanke-Oga-Sab-061411PSH">-1.5 (+145)</span></a></td>

				<td ><input type="checkbox" name="selection[Base-Range-Yanke-Oga-Sab-061411PSH]" id="selection[Base-Range-Yanke-Oga-Sab-061411PSH]" value="Base-Range-Yanke-Oga-Sab-061411|PSH|1|145|100|-3|+145" onclick="checkPoint(this.name)"></td>


				<td class="overUnder" align="right"><a title="Click to bet on the games total score to be under 8" target="TICKET" href="#" onclick="javascript:quickbet(getValueFromChkBox(\'selection[Base-Range-Yanke-Oga-Sab-061411TLU]\'),204, 1);return false; "><span id="Base-Range-Yanke-Oga-Sab-061411TLU">Under 8 (-120)</span></a></td>

				<td class="overUnder"><input type="checkbox" name="selection[Base-Range-Yanke-Oga-Sab-061411TLU]" id="selection[Base-Range-Yanke-Oga-Sab-061411TLU]" value="Base-Range-Yanke-Oga-Sab-061411|TLU|1|100|120|16|-120" onclick="checkPoint(this.name)"></td>


			</tr>

			<!-- Pending / Previous row -->

			

			<tr><td class="trLine" colspan=9></td></tr>

		
			
			
			
			
			

			<input name="events[Base-India-Tiger-Mas-Ver-061411]" id="events[Base-India-Tiger-Mas-Ver-061411]" type="hidden" value="Base-India-Tiger-Mas-Ver-061411^Tigers(Detroit)^Indians(Cleveland)^06-14-11 7:05 PM^06/14/11^7:05 PM^204" />
			

			<tr valign=middle class="even">
            	<td class="left">
	            	06/14/11 
            	</td>
            	
				<td align="center" width="30">969</td>
				<td class="left"><a title="Team Statistics" href="#" onclick="javascript:window.open(\'http://www.sportsbook.com/statfeed/betting/statfeed.php?page=mlb/mlbteam&teamid=CLEVELAND\', \'statfox\'); return false;">Indians(Cleveland)<br>Masterson</a></td>

				<td align="right"><a title="Click to bet on Indians(Cleveland) to win straight up." target="TICKET" href="#" onclick="javascript:quickbet(getValueFromChkBox(\'selection[Base-India-Tiger-Mas-Ver-061411MLA]\'),204, 1);return false; "><span id="Base-India-Tiger-Mas-Ver-061411MLA">+165</span></a></td>

				<td ><input type="checkbox" name="selection[Base-India-Tiger-Mas-Ver-061411MLA]" id="selection[Base-India-Tiger-Mas-Ver-061411MLA]" value="Base-India-Tiger-Mas-Ver-061411|MLA|1|165|100|0|+165" onclick="checkMoney(this.name)"></td>


				<td align="right"><a title="Click to bet on Indians(Cleveland) to cover the spread." target="TICKET" href="#" onclick="javascript:quickbet(getValueFromChkBox(\'selection[Base-India-Tiger-Mas-Ver-061411PSA]\'),204, 1);return false; "><span id="Base-India-Tiger-Mas-Ver-061411PSA">+1.5 (-145)</span></a></td>

				<td ><input type="checkbox" name="selection[Base-India-Tiger-Mas-Ver-061411PSA]" id="selection[Base-India-Tiger-Mas-Ver-061411PSA]" value="Base-India-Tiger-Mas-Ver-061411|PSA|1|100|145|3|-145" onclick="checkPoint(this.name)"></td>


				<td class="overUnder" align="right"><a title="Click to bet on the games total score to be over 7" target="TICKET" href="#" onclick="javascript:quickbet(getValueFromChkBox(\'selection[Base-India-Tiger-Mas-Ver-061411TLO]\'),204, 1);return false; "><span id="Base-India-Tiger-Mas-Ver-061411TLO">Over 7.5 (-105)</span></a></td>

				<td class="overUnder"><input type="checkbox" name="selection[Base-India-Tiger-Mas-Ver-061411TLO]" id="selection[Base-India-Tiger-Mas-Ver-061411TLO]" value="Base-India-Tiger-Mas-Ver-061411|TLO|1|100|105|15|-105" onclick="checkPoint(this.name)"></td>


           </tr>

			<!-- Pending / Previous row -->
			

 			

			
				<tr align="center" class="even">
					<td class="left"><a href="#" onclick="javascript:window.open(\'http://www.sportsbook.com/statfeed/betting/statfeed.php?page=mlb/expanded&gameid=20110614DETROIT\',\'statfox\'); return false;" >Matchup</a></td>

					
 					<td class="left"><a href="#" onclick="javascript:window.open(\'http://www.sportsbook.com/statfeed/betting/statfeed.php?page=mlb/trend&gameid=20110614DETROIT\',\'statfox\'); return false;" >Trend Sheet</a></td> 
							
					<!--  Teamname and lines -->
					<td class="left"></td>
					<td></td> <td></td> 	<td></td><td></td> 	 <td class="overUnder"></td><td class="overUnder"></td>
				</tr>
			

			<tr valign=middle class="even">
				<td class="left">

				19:05 ET 
				</td>
				
				<td align="center" width="30">970</td>
				<td class="left"><a title="Team Statistics" href="#" onclick="javascript:window.open(\'http://www.sportsbook.com/statfeed/betting/statfeed.php?page=mlb/mlbteam&teamid=DETROIT\', \'statfox\'); return false;">Tigers(Detroit)<br>Verlander</a></td>

				<td align="right"><a title="Click to bet on Tigers(Detroit) to win straight up." target="TICKET" href="#" onclick="javascript:quickbet(getValueFromChkBox(\'selection[Base-India-Tiger-Mas-Ver-061411MLH]\'),204, 1);return false; "><span id="Base-India-Tiger-Mas-Ver-061411MLH">-175</span></a></td>

				<td ><input type="checkbox" name="selection[Base-India-Tiger-Mas-Ver-061411MLH]" id="selection[Base-India-Tiger-Mas-Ver-061411MLH]" value="Base-India-Tiger-Mas-Ver-061411|MLH|1|100|175|0|-175" onclick="checkMoney(this.name)"></td>


				<td align="right"><a title="Click to bet on Tigers(Detroit) to cover the spread." target="TICKET" href="#" onclick="javascript:quickbet(getValueFromChkBox(\'selection[Base-India-Tiger-Mas-Ver-061411PSH]\'),204, 1);return false; "><span id="Base-India-Tiger-Mas-Ver-061411PSH">-1.5 (+125)</span></a></td>

				<td ><input type="checkbox" name="selection[Base-India-Tiger-Mas-Ver-061411PSH]" id="selection[Base-India-Tiger-Mas-Ver-061411PSH]" value="Base-India-Tiger-Mas-Ver-061411|PSH|1|125|100|-3|+125" onclick="checkPoint(this.name)"></td>


				<td class="overUnder" align="right"><a title="Click to bet on the games total score to be under 7" target="TICKET" href="#" onclick="javascript:quickbet(getValueFromChkBox(\'selection[Base-India-Tiger-Mas-Ver-061411TLU]\'),204, 1);return false; "><span id="Base-India-Tiger-Mas-Ver-061411TLU">Under 7.5 (-115)</span></a></td>

				<td class="overUnder"><input type="checkbox" name="selection[Base-India-Tiger-Mas-Ver-061411TLU]" id="selection[Base-India-Tiger-Mas-Ver-061411TLU]" value="Base-India-Tiger-Mas-Ver-061411|TLU|1|100|115|15|-115" onclick="checkPoint(this.name)"></td>

			</tr>

			<!-- Pending / Previous row -->
			

			<tr><td class="trLine" colspan=9></td></tr>

		
			
			
			
			
			

			<input name="events[Base-Oriol-BlueJ-Jak-Vil-061411]" id="events[Base-Oriol-BlueJ-Jak-Vil-061411]" type="hidden" value="Base-Oriol-BlueJ-Jak-Vil-061411^BlueJays(Toronto)^Orioles(Baltimore)^06-14-11 7:05 PM^06/14/11^7:05 PM^204" />
			

			<tr valign=middle class="odd">
            	<td class="left">
	            	06/14/11 
            	</td>

            	
				<td align="center" width="30">971</td>
				<td class="left"><a title="Team Statistics" href="#" onclick="javascript:window.open(\'http://www.sportsbook.com/statfeed/betting/statfeed.php?page=mlb/mlbteam&teamid=BALTIMORE\', \'statfox\'); return false;">Orioles(Baltimore)<br>Jakubauskas</a></td>

				<td align="right"><a title="Click to bet on Orioles(Baltimore) to win straight up." target="TICKET" href="#" onclick="javascript:quickbet(getValueFromChkBox(\'selection[Base-Oriol-BlueJ-Jak-Vil-061411MLA]\'),204, 1);return false; "><span id="Base-Oriol-BlueJ-Jak-Vil-061411MLA">+138</span></a></td>

				<td ><input type="checkbox" name="selection[Base-Oriol-BlueJ-Jak-Vil-061411MLA]" id="selection[Base-Oriol-BlueJ-Jak-Vil-061411MLA]" value="Base-Oriol-BlueJ-Jak-Vil-061411|MLA|1|138|100|0|+138" onclick="checkMoney(this.name)"></td>


				<td align="right"><a title="Click to bet on Orioles(Baltimore) to cover the spread." target="TICKET" href="#" onclick="javascript:quickbet(getValueFromChkBox(\'selection[Base-Oriol-BlueJ-Jak-Vil-061411PSA]\'),204, 1);return false; "><span id="Base-Oriol-BlueJ-Jak-Vil-061411PSA">+1.5 (-150)</span></a></td>

				<td ><input type="checkbox" name="selection[Base-Oriol-BlueJ-Jak-Vil-061411PSA]" id="selection[Base-Oriol-BlueJ-Jak-Vil-061411PSA]" value="Base-Oriol-BlueJ-Jak-Vil-061411|PSA|1|100|150|3|-150" onclick="checkPoint(this.name)"></td>


				<td class="overUnder" align="right"><a title="Click to bet on the games total score to be over 9" target="TICKET" href="#" onclick="javascript:quickbet(getValueFromChkBox(\'selection[Base-Oriol-BlueJ-Jak-Vil-061411TLO]\'),204, 1);return false; "><span id="Base-Oriol-BlueJ-Jak-Vil-061411TLO">Over 9 </span></a></td>

				<td class="overUnder"><input type="checkbox" name="selection[Base-Oriol-BlueJ-Jak-Vil-061411TLO]" id="selection[Base-Oriol-BlueJ-Jak-Vil-061411TLO]" value="Base-Oriol-BlueJ-Jak-Vil-061411|TLO|1|100|110|18|-110" onclick="checkPoint(this.name)"></td>


           </tr>

			<!-- Pending / Previous row -->

			

 			

			
				<tr align="center" class="odd">
					<td class="left"><a href="#" onclick="javascript:window.open(\'http://www.sportsbook.com/statfeed/betting/statfeed.php?page=mlb/expanded&gameid=20110614TORONTO\',\'statfox\'); return false;" >Matchup</a></td>
					
 					<td class="left"><a href="#" onclick="javascript:window.open(\'http://www.sportsbook.com/statfeed/betting/statfeed.php?page=mlb/trend&gameid=20110614TORONTO\',\'statfox\'); return false;" >Trend Sheet</a></td> 
							
					<!--  Teamname and lines -->
					<td class="left"></td>
					<td></td> <td></td> 	<td></td><td></td> 	 <td class="overUnder"></td><td class="overUnder"></td>
				</tr>

			

			<tr valign=middle class="odd">
				<td class="left">
				19:05 ET 
				</td>
				
				<td align="center" width="30">972</td>
				<td class="left"><a title="Team Statistics" href="#" onclick="javascript:window.open(\'http://www.sportsbook.com/statfeed/betting/statfeed.php?page=mlb/mlbteam&teamid=TORONTO\', \'statfox\'); return false;">BlueJays(Toronto)<br>Villanueva</a></td>

				<td align="right"><a title="Click to bet on BlueJays(Toronto) to win straight up." target="TICKET" href="#" onclick="javascript:quickbet(getValueFromChkBox(\'selection[Base-Oriol-BlueJ-Jak-Vil-061411MLH]\'),204, 1);return false; "><span id="Base-Oriol-BlueJ-Jak-Vil-061411MLH">-148</span></a></td>

				<td ><input type="checkbox" name="selection[Base-Oriol-BlueJ-Jak-Vil-061411MLH]" id="selection[Base-Oriol-BlueJ-Jak-Vil-061411MLH]" value="Base-Oriol-BlueJ-Jak-Vil-061411|MLH|1|100|148|0|-148" onclick="checkMoney(this.name)"></td>


				<td align="right"><a title="Click to bet on BlueJays(Toronto) to cover the spread." target="TICKET" href="#" onclick="javascript:quickbet(getValueFromChkBox(\'selection[Base-Oriol-BlueJ-Jak-Vil-061411PSH]\'),204, 1);return false; "><span id="Base-Oriol-BlueJ-Jak-Vil-061411PSH">-1.5 (+130)</span></a></td>

				<td ><input type="checkbox" name="selection[Base-Oriol-BlueJ-Jak-Vil-061411PSH]" id="selection[Base-Oriol-BlueJ-Jak-Vil-061411PSH]" value="Base-Oriol-BlueJ-Jak-Vil-061411|PSH|1|130|100|-3|+130" onclick="checkPoint(this.name)"></td>


				<td class="overUnder" align="right"><a title="Click to bet on the games total score to be under 9" target="TICKET" href="#" onclick="javascript:quickbet(getValueFromChkBox(\'selection[Base-Oriol-BlueJ-Jak-Vil-061411TLU]\'),204, 1);return false; "><span id="Base-Oriol-BlueJ-Jak-Vil-061411TLU">Under 9 </span></a></td>

				<td class="overUnder"><input type="checkbox" name="selection[Base-Oriol-BlueJ-Jak-Vil-061411TLU]" id="selection[Base-Oriol-BlueJ-Jak-Vil-061411TLU]" value="Base-Oriol-BlueJ-Jak-Vil-061411|TLU|1|100|110|18|-110" onclick="checkPoint(this.name)"></td>


			</tr>

			<!-- Pending / Previous row -->
			

			<tr><td class="trLine" colspan=9></td></tr>

		
			
			
			
			
			

			<input name="events[Base-Mets(-Brave-Nie-Jur-061411]" id="events[Base-Mets(-Brave-Nie-Jur-061411]" type="hidden" value="Base-Mets(-Brave-Nie-Jur-061411^Braves(Atlanta)^Mets(NY)^06-14-11 7:10 PM^06/14/11^7:10 PM^204" />
			

			<tr valign=middle class="even">
            	<td class="left">

	            	06/14/11 
            	</td>
            	
				<td align="center" width="30">955</td>
				<td class="left"><a title="Team Statistics" href="#" onclick="javascript:window.open(\'http://www.sportsbook.com/statfeed/betting/statfeed.php?page=mlb/mlbteam&teamid=NY METS\', \'statfox\'); return false;">Mets(NY)<br>Niese</a></td>

				<td align="right"><a title="Click to bet on Mets(NY) to win straight up." target="TICKET" href="#" onclick="javascript:quickbet(getValueFromChkBox(\'selection[Base-Mets(-Brave-Nie-Jur-061411MLA]\'),204, 1);return false; "><span id="Base-Mets(-Brave-Nie-Jur-061411MLA">+136</span></a></td>

				<td ><input type="checkbox" name="selection[Base-Mets(-Brave-Nie-Jur-061411MLA]" id="selection[Base-Mets(-Brave-Nie-Jur-061411MLA]" value="Base-Mets(-Brave-Nie-Jur-061411|MLA|1|136|100|0|+136" onclick="checkMoney(this.name)"></td>


				<td align="right"><a title="Click to bet on Mets(NY) to cover the spread." target="TICKET" href="#" onclick="javascript:quickbet(getValueFromChkBox(\'selection[Base-Mets(-Brave-Nie-Jur-061411PSA]\'),204, 1);return false; "><span id="Base-Mets(-Brave-Nie-Jur-061411PSA">+1.5 (-170)</span></a></td>

				<td ><input type="checkbox" name="selection[Base-Mets(-Brave-Nie-Jur-061411PSA]" id="selection[Base-Mets(-Brave-Nie-Jur-061411PSA]" value="Base-Mets(-Brave-Nie-Jur-061411|PSA|1|100|170|3|-170" onclick="checkPoint(this.name)"></td>


				<td class="overUnder" align="right"><a title="Click to bet on the games total score to be over 7" target="TICKET" href="#" onclick="javascript:quickbet(getValueFromChkBox(\'selection[Base-Mets(-Brave-Nie-Jur-061411TLO]\'),204, 1);return false; "><span id="Base-Mets(-Brave-Nie-Jur-061411TLO">Over 7 (-115)</span></a></td>

				<td class="overUnder"><input type="checkbox" name="selection[Base-Mets(-Brave-Nie-Jur-061411TLO]" id="selection[Base-Mets(-Brave-Nie-Jur-061411TLO]" value="Base-Mets(-Brave-Nie-Jur-061411|TLO|1|100|115|14|-115" onclick="checkPoint(this.name)"></td>

           </tr>

			<!-- Pending / Previous row -->
			

 			

			
				<tr align="center" class="even">
					<td class="left"><a href="#" onclick="javascript:window.open(\'http://www.sportsbook.com/statfeed/betting/statfeed.php?page=mlb/expanded&gameid=20110614ATLANTA\',\'statfox\'); return false;" >Matchup</a></td>
					
 					<td class="left"><a href="#" onclick="javascript:window.open(\'http://www.sportsbook.com/statfeed/betting/statfeed.php?page=mlb/trend&gameid=20110614ATLANTA\',\'statfox\'); return false;" >Trend Sheet</a></td> 
							
					<!--  Teamname and lines -->
					<td class="left"></td>

					<td></td> <td></td> 	<td></td><td></td> 	 <td class="overUnder"></td><td class="overUnder"></td>
				</tr>
			

			<tr valign=middle class="even">
				<td class="left">
				19:10 ET 
				</td>
				
				<td align="center" width="30">956</td>

				<td class="left"><a title="Team Statistics" href="#" onclick="javascript:window.open(\'http://www.sportsbook.com/statfeed/betting/statfeed.php?page=mlb/mlbteam&teamid=ATLANTA\', \'statfox\'); return false;">Braves(Atlanta)<br>Jurrjens</a></td>

				<td align="right"><a title="Click to bet on Braves(Atlanta) to win straight up." target="TICKET" href="#" onclick="javascript:quickbet(getValueFromChkBox(\'selection[Base-Mets(-Brave-Nie-Jur-061411MLH]\'),204, 1);return false; "><span id="Base-Mets(-Brave-Nie-Jur-061411MLH">-146</span></a></td>

				<td ><input type="checkbox" name="selection[Base-Mets(-Brave-Nie-Jur-061411MLH]" id="selection[Base-Mets(-Brave-Nie-Jur-061411MLH]" value="Base-Mets(-Brave-Nie-Jur-061411|MLH|1|100|146|0|-146" onclick="checkMoney(this.name)"></td>


				<td align="right"><a title="Click to bet on Braves(Atlanta) to cover the spread." target="TICKET" href="#" onclick="javascript:quickbet(getValueFromChkBox(\'selection[Base-Mets(-Brave-Nie-Jur-061411PSH]\'),204, 1);return false; "><span id="Base-Mets(-Brave-Nie-Jur-061411PSH">-1.5 (+150)</span></a></td>

				<td ><input type="checkbox" name="selection[Base-Mets(-Brave-Nie-Jur-061411PSH]" id="selection[Base-Mets(-Brave-Nie-Jur-061411PSH]" value="Base-Mets(-Brave-Nie-Jur-061411|PSH|1|150|100|-3|+150" onclick="checkPoint(this.name)"></td>


				<td class="overUnder" align="right"><a title="Click to bet on the games total score to be under 7" target="TICKET" href="#" onclick="javascript:quickbet(getValueFromChkBox(\'selection[Base-Mets(-Brave-Nie-Jur-061411TLU]\'),204, 1);return false; "><span id="Base-Mets(-Brave-Nie-Jur-061411TLU">Under 7 (-105)</span></a></td>

				<td class="overUnder"><input type="checkbox" name="selection[Base-Mets(-Brave-Nie-Jur-061411TLU]" id="selection[Base-Mets(-Brave-Nie-Jur-061411TLU]" value="Base-Mets(-Brave-Nie-Jur-061411|TLU|1|100|105|14|-105" onclick="checkPoint(this.name)"></td>


			</tr>

			<!-- Pending / Previous row -->

			

			<tr><td class="trLine" colspan=9></td></tr>

		
			
			
			
			
			

			<input name="events[Base-RedSo-Rays(-Wak-Shi-061411]" id="events[Base-RedSo-Rays(-Wak-Shi-061411]" type="hidden" value="Base-RedSo-Rays(-Wak-Shi-061411^Rays(TampaBay)^RedSox(Boston)^06-14-11 7:10 PM^06/14/11^7:10 PM^204" />
			

			<tr valign=middle class="odd">
            	<td class="left">
	            	06/14/11 
            	</td>
            	
				<td align="center" width="30">973</td>
				<td class="left"><a title="Team Statistics" href="#" onclick="javascript:window.open(\'http://www.sportsbook.com/statfeed/betting/statfeed.php?page=mlb/mlbteam&teamid=BOSTON\', \'statfox\'); return false;">RedSox(Boston)<br>Wakefield</a></td>

				<td align="right"><a title="Click to bet on RedSox(Boston) to win straight up." target="TICKET" href="#" onclick="javascript:quickbet(getValueFromChkBox(\'selection[Base-RedSo-Rays(-Wak-Shi-061411MLA]\'),204, 1);return false; "><span id="Base-RedSo-Rays(-Wak-Shi-061411MLA">+116</span></a></td>

				<td ><input type="checkbox" name="selection[Base-RedSo-Rays(-Wak-Shi-061411MLA]" id="selection[Base-RedSo-Rays(-Wak-Shi-061411MLA]" value="Base-RedSo-Rays(-Wak-Shi-061411|MLA|1|116|100|0|+116" onclick="checkMoney(this.name)"></td>


				<td align="right"><a title="Click to bet on RedSox(Boston) to cover the spread." target="TICKET" href="#" onclick="javascript:quickbet(getValueFromChkBox(\'selection[Base-RedSo-Rays(-Wak-Shi-061411PSA]\'),204, 1);return false; "><span id="Base-RedSo-Rays(-Wak-Shi-061411PSA">+1.5 (-175)</span></a></td>

				<td ><input type="checkbox" name="selection[Base-RedSo-Rays(-Wak-Shi-061411PSA]" id="selection[Base-RedSo-Rays(-Wak-Shi-061411PSA]" value="Base-RedSo-Rays(-Wak-Shi-061411|PSA|1|100|175|3|-175" onclick="checkPoint(this.name)"></td>


				<td class="overUnder" align="right"><a title="Click to bet on the games total score to be over 8" target="TICKET" href="#" onclick="javascript:quickbet(getValueFromChkBox(\'selection[Base-RedSo-Rays(-Wak-Shi-061411TLO]\'),204, 1);return false; "><span id="Base-RedSo-Rays(-Wak-Shi-061411TLO">Over 8.5 </span></a></td>

				<td class="overUnder"><input type="checkbox" name="selection[Base-RedSo-Rays(-Wak-Shi-061411TLO]" id="selection[Base-RedSo-Rays(-Wak-Shi-061411TLO]" value="Base-RedSo-Rays(-Wak-Shi-061411|TLO|1|100|110|17|-110" onclick="checkPoint(this.name)"></td>


           </tr>

			<!-- Pending / Previous row -->
			

 			

			
				<tr align="center" class="odd">
					<td class="left"><a href="#" onclick="javascript:window.open(\'http://www.sportsbook.com/statfeed/betting/statfeed.php?page=mlb/expanded&gameid=20110614TAMPABAY\',\'statfox\'); return false;" >Matchup</a></td>

					
 					<td class="left"><a href="#" onclick="javascript:window.open(\'http://www.sportsbook.com/statfeed/betting/statfeed.php?page=mlb/trend&gameid=20110614TAMPABAY\',\'statfox\'); return false;" >Trend Sheet</a></td> 
							
					<!--  Teamname and lines -->
					<td class="left"></td>
					<td></td> <td></td> 	<td></td><td></td> 	 <td class="overUnder"></td><td class="overUnder"></td>
				</tr>
			

			<tr valign=middle class="odd">
				<td class="left">

				19:10 ET 
				</td>
				
				<td align="center" width="30">974</td>
				<td class="left"><a title="Team Statistics" href="#" onclick="javascript:window.open(\'http://www.sportsbook.com/statfeed/betting/statfeed.php?page=mlb/mlbteam&teamid=TAMPA BAY\', \'statfox\'); return false;">Rays(TampaBay)<br>Shields</a></td>

				<td align="right"><a title="Click to bet on Rays(TampaBay) to win straight up." target="TICKET" href="#" onclick="javascript:quickbet(getValueFromChkBox(\'selection[Base-RedSo-Rays(-Wak-Shi-061411MLH]\'),204, 1);return false; "><span id="Base-RedSo-Rays(-Wak-Shi-061411MLH">-126</span></a></td>

				<td ><input type="checkbox" name="selection[Base-RedSo-Rays(-Wak-Shi-061411MLH]" id="selection[Base-RedSo-Rays(-Wak-Shi-061411MLH]" value="Base-RedSo-Rays(-Wak-Shi-061411|MLH|1|100|126|0|-126" onclick="checkMoney(this.name)"></td>


				<td align="right"><a title="Click to bet on Rays(TampaBay) to cover the spread." target="TICKET" href="#" onclick="javascript:quickbet(getValueFromChkBox(\'selection[Base-RedSo-Rays(-Wak-Shi-061411PSH]\'),204, 1);return false; "><span id="Base-RedSo-Rays(-Wak-Shi-061411PSH">-1.5 (+155)</span></a></td>

				<td ><input type="checkbox" name="selection[Base-RedSo-Rays(-Wak-Shi-061411PSH]" id="selection[Base-RedSo-Rays(-Wak-Shi-061411PSH]" value="Base-RedSo-Rays(-Wak-Shi-061411|PSH|1|155|100|-3|+155" onclick="checkPoint(this.name)"></td>


				<td class="overUnder" align="right"><a title="Click to bet on the games total score to be under 8" target="TICKET" href="#" onclick="javascript:quickbet(getValueFromChkBox(\'selection[Base-RedSo-Rays(-Wak-Shi-061411TLU]\'),204, 1);return false; "><span id="Base-RedSo-Rays(-Wak-Shi-061411TLU">Under 8.5 </span></a></td>

				<td class="overUnder"><input type="checkbox" name="selection[Base-RedSo-Rays(-Wak-Shi-061411TLU]" id="selection[Base-RedSo-Rays(-Wak-Shi-061411TLU]" value="Base-RedSo-Rays(-Wak-Shi-061411|TLU|1|100|110|17|-110" onclick="checkPoint(this.name)"></td>

			</tr>

			<!-- Pending / Previous row -->
			

			<tr><td class="trLine" colspan=9></td></tr>

		
			
			
			
			
			

			<input name="events[Base-Pirat-Astro-Kar-Nor-061411]" id="events[Base-Pirat-Astro-Kar-Nor-061411]" type="hidden" value="Base-Pirat-Astro-Kar-Nor-061411^Astros(Houston)^Pirates(Pittsburgh)^06-14-11 8:05 PM^06/14/11^8:05 PM^204" />
			

			<tr valign=middle class="even">
            	<td class="left">
	            	06/14/11 
            	</td>

            	
				<td align="center" width="30">957</td>
				<td class="left"><a title="Team Statistics" href="#" onclick="javascript:window.open(\'http://www.sportsbook.com/statfeed/betting/statfeed.php?page=mlb/mlbteam&teamid=PITTSBURGH\', \'statfox\'); return false;">Pirates(Pittsburgh)<br>Karstens</a></td>

				<td align="right"><a title="Click to bet on Pirates(Pittsburgh) to win straight up." target="TICKET" href="#" onclick="javascript:quickbet(getValueFromChkBox(\'selection[Base-Pirat-Astro-Kar-Nor-061411MLA]\'),204, 1);return false; "><span id="Base-Pirat-Astro-Kar-Nor-061411MLA">+111</span></a></td>

				<td ><input type="checkbox" name="selection[Base-Pirat-Astro-Kar-Nor-061411MLA]" id="selection[Base-Pirat-Astro-Kar-Nor-061411MLA]" value="Base-Pirat-Astro-Kar-Nor-061411|MLA|1|111|100|0|+111" onclick="checkMoney(this.name)"></td>


				<td align="right"><a title="Click to bet on Pirates(Pittsburgh) to cover the spread." target="TICKET" href="#" onclick="javascript:quickbet(getValueFromChkBox(\'selection[Base-Pirat-Astro-Kar-Nor-061411PSA]\'),204, 1);return false; "><span id="Base-Pirat-Astro-Kar-Nor-061411PSA">+1.5 (-210)</span></a></td>

				<td ><input type="checkbox" name="selection[Base-Pirat-Astro-Kar-Nor-061411PSA]" id="selection[Base-Pirat-Astro-Kar-Nor-061411PSA]" value="Base-Pirat-Astro-Kar-Nor-061411|PSA|1|100|210|3|-210" onclick="checkPoint(this.name)"></td>


				<td class="overUnder" align="right"><a title="Click to bet on the games total score to be over 7" target="TICKET" href="#" onclick="javascript:quickbet(getValueFromChkBox(\'selection[Base-Pirat-Astro-Kar-Nor-061411TLO]\'),204, 1);return false; "><span id="Base-Pirat-Astro-Kar-Nor-061411TLO">Over 7.5 (even)</span></a></td>

				<td class="overUnder"><input type="checkbox" name="selection[Base-Pirat-Astro-Kar-Nor-061411TLO]" id="selection[Base-Pirat-Astro-Kar-Nor-061411TLO]" value="Base-Pirat-Astro-Kar-Nor-061411|TLO|1|100|100|15|even" onclick="checkPoint(this.name)"></td>


           </tr>

			<!-- Pending / Previous row -->

			

 			

			
				<tr align="center" class="even">
					<td class="left"><a href="#" onclick="javascript:window.open(\'http://www.sportsbook.com/statfeed/betting/statfeed.php?page=mlb/expanded&gameid=20110614HOUSTON\',\'statfox\'); return false;" >Matchup</a></td>
					
 					<td class="left"><a href="#" onclick="javascript:window.open(\'http://www.sportsbook.com/statfeed/betting/statfeed.php?page=mlb/trend&gameid=20110614HOUSTON\',\'statfox\'); return false;" >Trend Sheet</a></td> 
							
					<!--  Teamname and lines -->
					<td class="left"></td>
					<td></td> <td></td> 	<td></td><td></td> 	 <td class="overUnder"></td><td class="overUnder"></td>
				</tr>

			

			<tr valign=middle class="even">
				<td class="left">
				20:05 ET 
				</td>
				
				<td align="center" width="30">958</td>
				<td class="left"><a title="Team Statistics" href="#" onclick="javascript:window.open(\'http://www.sportsbook.com/statfeed/betting/statfeed.php?page=mlb/mlbteam&teamid=HOUSTON\', \'statfox\'); return false;">Astros(Houston)<br>Norris</a></td>

				<td align="right"><a title="Click to bet on Astros(Houston) to win straight up." target="TICKET" href="#" onclick="javascript:quickbet(getValueFromChkBox(\'selection[Base-Pirat-Astro-Kar-Nor-061411MLH]\'),204, 1);return false; "><span id="Base-Pirat-Astro-Kar-Nor-061411MLH">-121</span></a></td>

				<td ><input type="checkbox" name="selection[Base-Pirat-Astro-Kar-Nor-061411MLH]" id="selection[Base-Pirat-Astro-Kar-Nor-061411MLH]" value="Base-Pirat-Astro-Kar-Nor-061411|MLH|1|100|121|0|-121" onclick="checkMoney(this.name)"></td>


				<td align="right"><a title="Click to bet on Astros(Houston) to cover the spread." target="TICKET" href="#" onclick="javascript:quickbet(getValueFromChkBox(\'selection[Base-Pirat-Astro-Kar-Nor-061411PSH]\'),204, 1);return false; "><span id="Base-Pirat-Astro-Kar-Nor-061411PSH">-1.5 (+175)</span></a></td>

				<td ><input type="checkbox" name="selection[Base-Pirat-Astro-Kar-Nor-061411PSH]" id="selection[Base-Pirat-Astro-Kar-Nor-061411PSH]" value="Base-Pirat-Astro-Kar-Nor-061411|PSH|1|175|100|-3|+175" onclick="checkPoint(this.name)"></td>


				<td class="overUnder" align="right"><a title="Click to bet on the games total score to be under 7" target="TICKET" href="#" onclick="javascript:quickbet(getValueFromChkBox(\'selection[Base-Pirat-Astro-Kar-Nor-061411TLU]\'),204, 1);return false; "><span id="Base-Pirat-Astro-Kar-Nor-061411TLU">Under 7.5 (-120)</span></a></td>

				<td class="overUnder"><input type="checkbox" name="selection[Base-Pirat-Astro-Kar-Nor-061411TLU]" id="selection[Base-Pirat-Astro-Kar-Nor-061411TLU]" value="Base-Pirat-Astro-Kar-Nor-061411|TLU|1|100|120|15|-120" onclick="checkPoint(this.name)"></td>


			</tr>

			<!-- Pending / Previous row -->
			

			<tr><td class="trLine" colspan=9></td></tr>

		
			
			
			
			
			

			<input name="events[Base-Brewe-Cubs(-Gal-Wel-061411]" id="events[Base-Brewe-Cubs(-Gal-Wel-061411]" type="hidden" value="Base-Brewe-Cubs(-Gal-Wel-061411^Cubs(Chicago)^Brewers(Milwaukee)^06-14-11 8:05 PM^06/14/11^8:05 PM^204" />
			

			<tr valign=middle class="odd">
            	<td class="left">

	            	06/14/11 
            	</td>
            	
				<td align="center" width="30">959</td>
				<td class="left"><a title="Team Statistics" href="#" onclick="javascript:window.open(\'http://www.sportsbook.com/statfeed/betting/statfeed.php?page=mlb/mlbteam&teamid=MILWAUKEE\', \'statfox\'); return false;">Brewers(Milwaukee)<br>Gallardo</a></td>

				<td align="right"><a title="Click to bet on Brewers(Milwaukee) to win straight up." target="TICKET" href="#" onclick="javascript:quickbet(getValueFromChkBox(\'selection[Base-Brewe-Cubs(-Gal-Wel-061411MLA]\'),204, 1);return false; "><span id="Base-Brewe-Cubs(-Gal-Wel-061411MLA">-138</span></a></td>

				<td ><input type="checkbox" name="selection[Base-Brewe-Cubs(-Gal-Wel-061411MLA]" id="selection[Base-Brewe-Cubs(-Gal-Wel-061411MLA]" value="Base-Brewe-Cubs(-Gal-Wel-061411|MLA|1|100|138|0|-138" onclick="checkMoney(this.name)"></td>


				<td align="right"><a title="Click to bet on Brewers(Milwaukee) to cover the spread." target="TICKET" href="#" onclick="javascript:quickbet(getValueFromChkBox(\'selection[Base-Brewe-Cubs(-Gal-Wel-061411PSA]\'),204, 1);return false; "><span id="Base-Brewe-Cubs(-Gal-Wel-061411PSA">-1.5 (+115)</span></a></td>

				<td ><input type="checkbox" name="selection[Base-Brewe-Cubs(-Gal-Wel-061411PSA]" id="selection[Base-Brewe-Cubs(-Gal-Wel-061411PSA]" value="Base-Brewe-Cubs(-Gal-Wel-061411|PSA|1|115|100|-3|+115" onclick="checkPoint(this.name)"></td>


				<td class="overUnder" align="right"><a title="Click to bet on the games total score to be over 8" target="TICKET" href="#" onclick="javascript:quickbet(getValueFromChkBox(\'selection[Base-Brewe-Cubs(-Gal-Wel-061411TLO]\'),204, 1);return false; "><span id="Base-Brewe-Cubs(-Gal-Wel-061411TLO">Over 8 (-105)</span></a></td>

				<td class="overUnder"><input type="checkbox" name="selection[Base-Brewe-Cubs(-Gal-Wel-061411TLO]" id="selection[Base-Brewe-Cubs(-Gal-Wel-061411TLO]" value="Base-Brewe-Cubs(-Gal-Wel-061411|TLO|1|100|105|16|-105" onclick="checkPoint(this.name)"></td>

           </tr>

			<!-- Pending / Previous row -->
			

 			

			
				<tr align="center" class="odd">
					<td class="left"><a href="#" onclick="javascript:window.open(\'http://www.sportsbook.com/statfeed/betting/statfeed.php?page=mlb/expanded&gameid=20110614CHICAGOCUBS\',\'statfox\'); return false;" >Matchup</a></td>
					
 					<td class="left"><a href="#" onclick="javascript:window.open(\'http://www.sportsbook.com/statfeed/betting/statfeed.php?page=mlb/trend&gameid=20110614CHICAGOCUBS\',\'statfox\'); return false;" >Trend Sheet</a></td> 
							
					<!--  Teamname and lines -->
					<td class="left"></td>

					<td></td> <td></td> 	<td></td><td></td> 	 <td class="overUnder"></td><td class="overUnder"></td>
				</tr>
			

			<tr valign=middle class="odd">
				<td class="left">
				20:05 ET 
				</td>
				
				<td align="center" width="30">960</td>

				<td class="left"><a title="Team Statistics" href="#" onclick="javascript:window.open(\'http://www.sportsbook.com/statfeed/betting/statfeed.php?page=mlb/mlbteam&teamid=CHICAGO CUBS\', \'statfox\'); return false;">Cubs(Chicago)<br>Wells</a></td>

				<td align="right"><a title="Click to bet on Cubs(Chicago) to win straight up." target="TICKET" href="#" onclick="javascript:quickbet(getValueFromChkBox(\'selection[Base-Brewe-Cubs(-Gal-Wel-061411MLH]\'),204, 1);return false; "><span id="Base-Brewe-Cubs(-Gal-Wel-061411MLH">+128</span></a></td>

				<td ><input type="checkbox" name="selection[Base-Brewe-Cubs(-Gal-Wel-061411MLH]" id="selection[Base-Brewe-Cubs(-Gal-Wel-061411MLH]" value="Base-Brewe-Cubs(-Gal-Wel-061411|MLH|1|128|100|0|+128" onclick="checkMoney(this.name)"></td>


				<td align="right"><a title="Click to bet on Cubs(Chicago) to cover the spread." target="TICKET" href="#" onclick="javascript:quickbet(getValueFromChkBox(\'selection[Base-Brewe-Cubs(-Gal-Wel-061411PSH]\'),204, 1);return false; "><span id="Base-Brewe-Cubs(-Gal-Wel-061411PSH">+1.5 (-135)</span></a></td>

				<td ><input type="checkbox" name="selection[Base-Brewe-Cubs(-Gal-Wel-061411PSH]" id="selection[Base-Brewe-Cubs(-Gal-Wel-061411PSH]" value="Base-Brewe-Cubs(-Gal-Wel-061411|PSH|1|100|135|3|-135" onclick="checkPoint(this.name)"></td>


				<td class="overUnder" align="right"><a title="Click to bet on the games total score to be under 8" target="TICKET" href="#" onclick="javascript:quickbet(getValueFromChkBox(\'selection[Base-Brewe-Cubs(-Gal-Wel-061411TLU]\'),204, 1);return false; "><span id="Base-Brewe-Cubs(-Gal-Wel-061411TLU">Under 8 (-115)</span></a></td>

				<td class="overUnder"><input type="checkbox" name="selection[Base-Brewe-Cubs(-Gal-Wel-061411TLU]" id="selection[Base-Brewe-Cubs(-Gal-Wel-061411TLU]" value="Base-Brewe-Cubs(-Gal-Wel-061411|TLU|1|100|115|16|-115" onclick="checkPoint(this.name)"></td>


			</tr>

			<!-- Pending / Previous row -->

			

			<tr><td class="trLine" colspan=9></td></tr>

		
			
			
			
			
			

			<input name="events[Base-White-Twins-Flo-Pav-061411]" id="events[Base-White-Twins-Flo-Pav-061411]" type="hidden" value="Base-White-Twins-Flo-Pav-061411^Twins(Minnesota)^WhiteSox(Chicago)^06-14-11 8:10 PM^06/14/11^8:10 PM^204" />
			

			<tr valign=middle class="even">
            	<td class="left">
	            	06/14/11 
            	</td>
            	
				<td align="center" width="30">975</td>
				<td class="left"><a title="Team Statistics" href="#" onclick="javascript:window.open(\'http://www.sportsbook.com/statfeed/betting/statfeed.php?page=mlb/mlbteam&teamid=CHI WHITE SOX\', \'statfox\'); return false;">WhiteSox(Chicago)<br>Floyd</a></td>

				<td align="right"><a title="Click to bet on WhiteSox(Chicago) to win straight up." target="TICKET" href="#" onclick="javascript:quickbet(getValueFromChkBox(\'selection[Base-White-Twins-Flo-Pav-061411MLA]\'),204, 1);return false; "><span id="Base-White-Twins-Flo-Pav-061411MLA">-128</span></a></td>

				<td ><input type="checkbox" name="selection[Base-White-Twins-Flo-Pav-061411MLA]" id="selection[Base-White-Twins-Flo-Pav-061411MLA]" value="Base-White-Twins-Flo-Pav-061411|MLA|1|100|128|0|-128" onclick="checkMoney(this.name)"></td>


				<td align="right"><a title="Click to bet on WhiteSox(Chicago) to cover the spread." target="TICKET" href="#" onclick="javascript:quickbet(getValueFromChkBox(\'selection[Base-White-Twins-Flo-Pav-061411PSA]\'),204, 1);return false; "><span id="Base-White-Twins-Flo-Pav-061411PSA">-1.5 (+130)</span></a></td>

				<td ><input type="checkbox" name="selection[Base-White-Twins-Flo-Pav-061411PSA]" id="selection[Base-White-Twins-Flo-Pav-061411PSA]" value="Base-White-Twins-Flo-Pav-061411|PSA|1|130|100|-3|+130" onclick="checkPoint(this.name)"></td>


				<td class="overUnder" align="right"><a title="Click to bet on the games total score to be over 7" target="TICKET" href="#" onclick="javascript:quickbet(getValueFromChkBox(\'selection[Base-White-Twins-Flo-Pav-061411TLO]\'),204, 1);return false; "><span id="Base-White-Twins-Flo-Pav-061411TLO">Over 7.5 (-120)</span></a></td>

				<td class="overUnder"><input type="checkbox" name="selection[Base-White-Twins-Flo-Pav-061411TLO]" id="selection[Base-White-Twins-Flo-Pav-061411TLO]" value="Base-White-Twins-Flo-Pav-061411|TLO|1|100|120|15|-120" onclick="checkPoint(this.name)"></td>


           </tr>

			<!-- Pending / Previous row -->
			

 			

			
				<tr align="center" class="even">
					<td class="left"><a href="#" onclick="javascript:window.open(\'http://www.sportsbook.com/statfeed/betting/statfeed.php?page=mlb/expanded&gameid=20110614MINNESOTA\',\'statfox\'); return false;" >Matchup</a></td>

					
 					<td class="left"><a href="#" onclick="javascript:window.open(\'http://www.sportsbook.com/statfeed/betting/statfeed.php?page=mlb/trend&gameid=20110614MINNESOTA\',\'statfox\'); return false;" >Trend Sheet</a></td> 
							
					<!--  Teamname and lines -->
					<td class="left"></td>
					<td></td> <td></td> 	<td></td><td></td> 	 <td class="overUnder"></td><td class="overUnder"></td>
				</tr>
			

			<tr valign=middle class="even">
				<td class="left">

				20:10 ET 
				</td>
				
				<td align="center" width="30">976</td>
				<td class="left"><a title="Team Statistics" href="#" onclick="javascript:window.open(\'http://www.sportsbook.com/statfeed/betting/statfeed.php?page=mlb/mlbteam&teamid=MINNESOTA\', \'statfox\'); return false;">Twins(Minnesota)<br>Pavano</a></td>

				<td align="right"><a title="Click to bet on Twins(Minnesota) to win straight up." target="TICKET" href="#" onclick="javascript:quickbet(getValueFromChkBox(\'selection[Base-White-Twins-Flo-Pav-061411MLH]\'),204, 1);return false; "><span id="Base-White-Twins-Flo-Pav-061411MLH">+118</span></a></td>

				<td ><input type="checkbox" name="selection[Base-White-Twins-Flo-Pav-061411MLH]" id="selection[Base-White-Twins-Flo-Pav-061411MLH]" value="Base-White-Twins-Flo-Pav-061411|MLH|1|118|100|0|+118" onclick="checkMoney(this.name)"></td>


				<td align="right"><a title="Click to bet on Twins(Minnesota) to cover the spread." target="TICKET" href="#" onclick="javascript:quickbet(getValueFromChkBox(\'selection[Base-White-Twins-Flo-Pav-061411PSH]\'),204, 1);return false; "><span id="Base-White-Twins-Flo-Pav-061411PSH">+1.5 (-150)</span></a></td>

				<td ><input type="checkbox" name="selection[Base-White-Twins-Flo-Pav-061411PSH]" id="selection[Base-White-Twins-Flo-Pav-061411PSH]" value="Base-White-Twins-Flo-Pav-061411|PSH|1|100|150|3|-150" onclick="checkPoint(this.name)"></td>


				<td class="overUnder" align="right"><a title="Click to bet on the games total score to be under 7" target="TICKET" href="#" onclick="javascript:quickbet(getValueFromChkBox(\'selection[Base-White-Twins-Flo-Pav-061411TLU]\'),204, 1);return false; "><span id="Base-White-Twins-Flo-Pav-061411TLU">Under 7.5 (even)</span></a></td>

				<td class="overUnder"><input type="checkbox" name="selection[Base-White-Twins-Flo-Pav-061411TLU]" id="selection[Base-White-Twins-Flo-Pav-061411TLU]" value="Base-White-Twins-Flo-Pav-061411|TLU|1|100|100|15|even" onclick="checkPoint(this.name)"></td>

			</tr>

			<!-- Pending / Previous row -->
			

			<tr><td class="trLine" colspan=9></td></tr>

		
			
			
			
			
			

			<input name="events[Base-Padre-Rocki-LeB-Nic-061411]" id="events[Base-Padre-Rocki-LeB-Nic-061411]" type="hidden" value="Base-Padre-Rocki-LeB-Nic-061411^Rockies(Colorado)^Padres(SanDiego)^06-14-11 8:40 PM^06/14/11^8:40 PM^204" />
			

			<tr valign=middle class="odd">
            	<td class="left">
	            	06/14/11 
            	</td>

            	
				<td align="center" width="30">961</td>
				<td class="left"><a title="Team Statistics" href="#" onclick="javascript:window.open(\'http://www.sportsbook.com/statfeed/betting/statfeed.php?page=mlb/mlbteam&teamid=SAN DIEGO\', \'statfox\'); return false;">Padres(SanDiego)<br>LeBlanc</a></td>

				<td align="right"><a title="Click to bet on Padres(SanDiego) to win straight up." target="TICKET" href="#" onclick="javascript:quickbet(getValueFromChkBox(\'selection[Base-Padre-Rocki-LeB-Nic-061411MLA]\'),204, 1);return false; "><span id="Base-Padre-Rocki-LeB-Nic-061411MLA">+153</span></a></td>

				<td ><input type="checkbox" name="selection[Base-Padre-Rocki-LeB-Nic-061411MLA]" id="selection[Base-Padre-Rocki-LeB-Nic-061411MLA]" value="Base-Padre-Rocki-LeB-Nic-061411|MLA|1|153|100|0|+153" onclick="checkMoney(this.name)"></td>


				<td align="right"><a title="Click to bet on Padres(SanDiego) to cover the spread." target="TICKET" href="#" onclick="javascript:quickbet(getValueFromChkBox(\'selection[Base-Padre-Rocki-LeB-Nic-061411PSA]\'),204, 1);return false; "><span id="Base-Padre-Rocki-LeB-Nic-061411PSA">+1.5 (-140)</span></a></td>

				<td ><input type="checkbox" name="selection[Base-Padre-Rocki-LeB-Nic-061411PSA]" id="selection[Base-Padre-Rocki-LeB-Nic-061411PSA]" value="Base-Padre-Rocki-LeB-Nic-061411|PSA|1|100|140|3|-140" onclick="checkPoint(this.name)"></td>


				<td class="overUnder" align="right"><a title="Click to bet on the games total score to be over 10" target="TICKET" href="#" onclick="javascript:quickbet(getValueFromChkBox(\'selection[Base-Padre-Rocki-LeB-Nic-061411TLO]\'),204, 1);return false; "><span id="Base-Padre-Rocki-LeB-Nic-061411TLO">Over 10 (-105)</span></a></td>

				<td class="overUnder"><input type="checkbox" name="selection[Base-Padre-Rocki-LeB-Nic-061411TLO]" id="selection[Base-Padre-Rocki-LeB-Nic-061411TLO]" value="Base-Padre-Rocki-LeB-Nic-061411|TLO|1|100|105|20|-105" onclick="checkPoint(this.name)"></td>


           </tr>

			<!-- Pending / Previous row -->

			

 			

			
				<tr align="center" class="odd">
					<td class="left"><a href="#" onclick="javascript:window.open(\'http://www.sportsbook.com/statfeed/betting/statfeed.php?page=mlb/expanded&gameid=20110614COLORADO\',\'statfox\'); return false;" >Matchup</a></td>
					
 					<td class="left"><a href="#" onclick="javascript:window.open(\'http://www.sportsbook.com/statfeed/betting/statfeed.php?page=mlb/trend&gameid=20110614COLORADO\',\'statfox\'); return false;" >Trend Sheet</a></td> 
							
					<!--  Teamname and lines -->
					<td class="left"></td>
					<td></td> <td></td> 	<td></td><td></td> 	 <td class="overUnder"></td><td class="overUnder"></td>
				</tr>

			

			<tr valign=middle class="odd">
				<td class="left">
				20:40 ET 
				</td>
				
				<td align="center" width="30">962</td>
				<td class="left"><a title="Team Statistics" href="#" onclick="javascript:window.open(\'http://www.sportsbook.com/statfeed/betting/statfeed.php?page=mlb/mlbteam&teamid=COLORADO\', \'statfox\'); return false;">Rockies(Colorado)<br>Nicasio</a></td>

				<td align="right"><a title="Click to bet on Rockies(Colorado) to win straight up." target="TICKET" href="#" onclick="javascript:quickbet(getValueFromChkBox(\'selection[Base-Padre-Rocki-LeB-Nic-061411MLH]\'),204, 1);return false; "><span id="Base-Padre-Rocki-LeB-Nic-061411MLH">-163</span></a></td>

				<td ><input type="checkbox" name="selection[Base-Padre-Rocki-LeB-Nic-061411MLH]" id="selection[Base-Padre-Rocki-LeB-Nic-061411MLH]" value="Base-Padre-Rocki-LeB-Nic-061411|MLH|1|100|163|0|-163" onclick="checkMoney(this.name)"></td>


				<td align="right"><a title="Click to bet on Rockies(Colorado) to cover the spread." target="TICKET" href="#" onclick="javascript:quickbet(getValueFromChkBox(\'selection[Base-Padre-Rocki-LeB-Nic-061411PSH]\'),204, 1);return false; "><span id="Base-Padre-Rocki-LeB-Nic-061411PSH">-1.5 (+120)</span></a></td>

				<td ><input type="checkbox" name="selection[Base-Padre-Rocki-LeB-Nic-061411PSH]" id="selection[Base-Padre-Rocki-LeB-Nic-061411PSH]" value="Base-Padre-Rocki-LeB-Nic-061411|PSH|1|120|100|-3|+120" onclick="checkPoint(this.name)"></td>


				<td class="overUnder" align="right"><a title="Click to bet on the games total score to be under 10" target="TICKET" href="#" onclick="javascript:quickbet(getValueFromChkBox(\'selection[Base-Padre-Rocki-LeB-Nic-061411TLU]\'),204, 1);return false; "><span id="Base-Padre-Rocki-LeB-Nic-061411TLU">Under 10 (-115)</span></a></td>

				<td class="overUnder"><input type="checkbox" name="selection[Base-Padre-Rocki-LeB-Nic-061411TLU]" id="selection[Base-Padre-Rocki-LeB-Nic-061411TLU]" value="Base-Padre-Rocki-LeB-Nic-061411|TLU|1|100|115|20|-115" onclick="checkPoint(this.name)"></td>


			</tr>

			<!-- Pending / Previous row -->
			

			<tr><td class="trLine" colspan=9></td></tr>

		
			
			
			
			
			

			<input name="events[Base-Giant-Diamo-Cai-Col-061411]" id="events[Base-Giant-Diamo-Cai-Col-061411]" type="hidden" value="Base-Giant-Diamo-Cai-Col-061411^Diamondbacks(Arizona)^Giants(SanFrancisco)^06-14-11 9:40 PM^06/14/11^9:40 PM^204" />
			

			<tr valign=middle class="even">
            	<td class="left">

	            	06/14/11 
            	</td>
            	
				<td align="center" width="30">963</td>
				<td class="left"><a title="Team Statistics" href="#" onclick="javascript:window.open(\'http://www.sportsbook.com/statfeed/betting/statfeed.php?page=mlb/mlbteam&teamid=SAN FRANCISCO\', \'statfox\'); return false;">Giants(SanFrancisco)<br>Cain</a></td>

				<td align="right"><a title="Click to bet on Giants(SanFrancisco) to win straight up." target="TICKET" href="#" onclick="javascript:quickbet(getValueFromChkBox(\'selection[Base-Giant-Diamo-Cai-Col-061411MLA]\'),204, 1);return false; "><span id="Base-Giant-Diamo-Cai-Col-061411MLA">+107</span></a></td>

				<td ><input type="checkbox" name="selection[Base-Giant-Diamo-Cai-Col-061411MLA]" id="selection[Base-Giant-Diamo-Cai-Col-061411MLA]" value="Base-Giant-Diamo-Cai-Col-061411|MLA|1|107|100|0|+107" onclick="checkMoney(this.name)"></td>


				<td align="right"><a title="Click to bet on Giants(SanFrancisco) to cover the spread." target="TICKET" href="#" onclick="javascript:quickbet(getValueFromChkBox(\'selection[Base-Giant-Diamo-Cai-Col-061411PSA]\'),204, 1);return false; "><span id="Base-Giant-Diamo-Cai-Col-061411PSA">+1.5 (-210)</span></a></td>

				<td ><input type="checkbox" name="selection[Base-Giant-Diamo-Cai-Col-061411PSA]" id="selection[Base-Giant-Diamo-Cai-Col-061411PSA]" value="Base-Giant-Diamo-Cai-Col-061411|PSA|1|100|210|3|-210" onclick="checkPoint(this.name)"></td>


				<td class="overUnder" align="right"><a title="Click to bet on the games total score to be over 7" target="TICKET" href="#" onclick="javascript:quickbet(getValueFromChkBox(\'selection[Base-Giant-Diamo-Cai-Col-061411TLO]\'),204, 1);return false; "><span id="Base-Giant-Diamo-Cai-Col-061411TLO">Over 7.5 (-105)</span></a></td>

				<td class="overUnder"><input type="checkbox" name="selection[Base-Giant-Diamo-Cai-Col-061411TLO]" id="selection[Base-Giant-Diamo-Cai-Col-061411TLO]" value="Base-Giant-Diamo-Cai-Col-061411|TLO|1|100|105|15|-105" onclick="checkPoint(this.name)"></td>

           </tr>

			<!-- Pending / Previous row -->
			

 			

			
				<tr align="center" class="even">
					<td class="left"><a href="#" onclick="javascript:window.open(\'http://www.sportsbook.com/statfeed/betting/statfeed.php?page=mlb/expanded&gameid=20110614ARIZONA\',\'statfox\'); return false;" >Matchup</a></td>
					
 					<td class="left"><a href="#" onclick="javascript:window.open(\'http://www.sportsbook.com/statfeed/betting/statfeed.php?page=mlb/trend&gameid=20110614ARIZONA\',\'statfox\'); return false;" >Trend Sheet</a></td> 
							
					<!--  Teamname and lines -->
					<td class="left"></td>

					<td></td> <td></td> 	<td></td><td></td> 	 <td class="overUnder"></td><td class="overUnder"></td>
				</tr>
			

			<tr valign=middle class="even">
				<td class="left">
				21:40 ET 
				</td>
				
				<td align="center" width="30">964</td>

				<td class="left"><a title="Team Statistics" href="#" onclick="javascript:window.open(\'http://www.sportsbook.com/statfeed/betting/statfeed.php?page=mlb/mlbteam&teamid=ARIZONA\', \'statfox\'); return false;">Diamondbacks(Arizona)<br>Collmenter</a></td>

				<td align="right"><a title="Click to bet on Diamondbacks(Arizona) to win straight up." target="TICKET" href="#" onclick="javascript:quickbet(getValueFromChkBox(\'selection[Base-Giant-Diamo-Cai-Col-061411MLH]\'),204, 1);return false; "><span id="Base-Giant-Diamo-Cai-Col-061411MLH">-117</span></a></td>

				<td ><input type="checkbox" name="selection[Base-Giant-Diamo-Cai-Col-061411MLH]" id="selection[Base-Giant-Diamo-Cai-Col-061411MLH]" value="Base-Giant-Diamo-Cai-Col-061411|MLH|1|100|117|0|-117" onclick="checkMoney(this.name)"></td>


				<td align="right"><a title="Click to bet on Diamondbacks(Arizona) to cover the spread." target="TICKET" href="#" onclick="javascript:quickbet(getValueFromChkBox(\'selection[Base-Giant-Diamo-Cai-Col-061411PSH]\'),204, 1);return false; "><span id="Base-Giant-Diamo-Cai-Col-061411PSH">-1.5 (+175)</span></a></td>

				<td ><input type="checkbox" name="selection[Base-Giant-Diamo-Cai-Col-061411PSH]" id="selection[Base-Giant-Diamo-Cai-Col-061411PSH]" value="Base-Giant-Diamo-Cai-Col-061411|PSH|1|175|100|-3|+175" onclick="checkPoint(this.name)"></td>


				<td class="overUnder" align="right"><a title="Click to bet on the games total score to be under 7" target="TICKET" href="#" onclick="javascript:quickbet(getValueFromChkBox(\'selection[Base-Giant-Diamo-Cai-Col-061411TLU]\'),204, 1);return false; "><span id="Base-Giant-Diamo-Cai-Col-061411TLU">Under 7.5 (-115)</span></a></td>

				<td class="overUnder"><input type="checkbox" name="selection[Base-Giant-Diamo-Cai-Col-061411TLU]" id="selection[Base-Giant-Diamo-Cai-Col-061411TLU]" value="Base-Giant-Diamo-Cai-Col-061411|TLU|1|100|115|15|-115" onclick="checkPoint(this.name)"></td>


			</tr>

			<!-- Pending / Previous row -->

			

			<tr><td class="trLine" colspan=9></td></tr>

		
			
			
			
			
			

			<input name="events[Base-Royal-Athle-Duf-Cah-061411]" id="events[Base-Royal-Athle-Duf-Cah-061411]" type="hidden" value="Base-Royal-Athle-Duf-Cah-061411^Athletics(Oakland)^Royals(KansasCity)^06-14-11 10:05 PM^06/14/11^10:05 PM^204" />
			

			<tr valign=middle class="odd">
            	<td class="left">
	            	06/14/11 
            	</td>
            	
				<td align="center" width="30">977</td>
				<td class="left"><a title="Team Statistics" href="#" onclick="javascript:window.open(\'http://www.sportsbook.com/statfeed/betting/statfeed.php?page=mlb/mlbteam&teamid=KANSAS CITY\', \'statfox\'); return false;">Royals(KansasCity)<br>Duffy</a></td>

				<td align="right"><a title="Click to bet on Royals(KansasCity) to win straight up." target="TICKET" href="#" onclick="javascript:quickbet(getValueFromChkBox(\'selection[Base-Royal-Athle-Duf-Cah-061411MLA]\'),204, 1);return false; "><span id="Base-Royal-Athle-Duf-Cah-061411MLA">+153</span></a></td>

				<td ><input type="checkbox" name="selection[Base-Royal-Athle-Duf-Cah-061411MLA]" id="selection[Base-Royal-Athle-Duf-Cah-061411MLA]" value="Base-Royal-Athle-Duf-Cah-061411|MLA|1|153|100|0|+153" onclick="checkMoney(this.name)"></td>


				<td align="right"><a title="Click to bet on Royals(KansasCity) to cover the spread." target="TICKET" href="#" onclick="javascript:quickbet(getValueFromChkBox(\'selection[Base-Royal-Athle-Duf-Cah-061411PSA]\'),204, 1);return false; "><span id="Base-Royal-Athle-Duf-Cah-061411PSA">+1.5 (-155)</span></a></td>

				<td ><input type="checkbox" name="selection[Base-Royal-Athle-Duf-Cah-061411PSA]" id="selection[Base-Royal-Athle-Duf-Cah-061411PSA]" value="Base-Royal-Athle-Duf-Cah-061411|PSA|1|100|155|3|-155" onclick="checkPoint(this.name)"></td>


				<td class="overUnder" align="right"><a title="Click to bet on the games total score to be over 7" target="TICKET" href="#" onclick="javascript:quickbet(getValueFromChkBox(\'selection[Base-Royal-Athle-Duf-Cah-061411TLO]\'),204, 1);return false; "><span id="Base-Royal-Athle-Duf-Cah-061411TLO">Over 7.5 (-115)</span></a></td>

				<td class="overUnder"><input type="checkbox" name="selection[Base-Royal-Athle-Duf-Cah-061411TLO]" id="selection[Base-Royal-Athle-Duf-Cah-061411TLO]" value="Base-Royal-Athle-Duf-Cah-061411|TLO|1|100|115|15|-115" onclick="checkPoint(this.name)"></td>


           </tr>

			<!-- Pending / Previous row -->
			

 			

			
				<tr align="center" class="odd">
					<td class="left"><a href="#" onclick="javascript:window.open(\'http://www.sportsbook.com/statfeed/betting/statfeed.php?page=mlb/expanded&gameid=20110614OAKLAND\',\'statfox\'); return false;" >Matchup</a></td>

					
 					<td class="left"><a href="#" onclick="javascript:window.open(\'http://www.sportsbook.com/statfeed/betting/statfeed.php?page=mlb/trend&gameid=20110614OAKLAND\',\'statfox\'); return false;" >Trend Sheet</a></td> 
							
					<!--  Teamname and lines -->
					<td class="left"></td>
					<td></td> <td></td> 	<td></td><td></td> 	 <td class="overUnder"></td><td class="overUnder"></td>
				</tr>
			

			<tr valign=middle class="odd">
				<td class="left">

				22:05 ET 
				</td>
				
				<td align="center" width="30">978</td>
				<td class="left"><a title="Team Statistics" href="#" onclick="javascript:window.open(\'http://www.sportsbook.com/statfeed/betting/statfeed.php?page=mlb/mlbteam&teamid=OAKLAND\', \'statfox\'); return false;">Athletics(Oakland)<br>Cahill</a></td>

				<td align="right"><a title="Click to bet on Athletics(Oakland) to win straight up." target="TICKET" href="#" onclick="javascript:quickbet(getValueFromChkBox(\'selection[Base-Royal-Athle-Duf-Cah-061411MLH]\'),204, 1);return false; "><span id="Base-Royal-Athle-Duf-Cah-061411MLH">-163</span></a></td>

				<td ><input type="checkbox" name="selection[Base-Royal-Athle-Duf-Cah-061411MLH]" id="selection[Base-Royal-Athle-Duf-Cah-061411MLH]" value="Base-Royal-Athle-Duf-Cah-061411|MLH|1|100|163|0|-163" onclick="checkMoney(this.name)"></td>


				<td align="right"><a title="Click to bet on Athletics(Oakland) to cover the spread." target="TICKET" href="#" onclick="javascript:quickbet(getValueFromChkBox(\'selection[Base-Royal-Athle-Duf-Cah-061411PSH]\'),204, 1);return false; "><span id="Base-Royal-Athle-Duf-Cah-061411PSH">-1.5 (+135)</span></a></td>

				<td ><input type="checkbox" name="selection[Base-Royal-Athle-Duf-Cah-061411PSH]" id="selection[Base-Royal-Athle-Duf-Cah-061411PSH]" value="Base-Royal-Athle-Duf-Cah-061411|PSH|1|135|100|-3|+135" onclick="checkPoint(this.name)"></td>


				<td class="overUnder" align="right"><a title="Click to bet on the games total score to be under 7" target="TICKET" href="#" onclick="javascript:quickbet(getValueFromChkBox(\'selection[Base-Royal-Athle-Duf-Cah-061411TLU]\'),204, 1);return false; "><span id="Base-Royal-Athle-Duf-Cah-061411TLU">Under 7.5 (-105)</span></a></td>

				<td class="overUnder"><input type="checkbox" name="selection[Base-Royal-Athle-Duf-Cah-061411TLU]" id="selection[Base-Royal-Athle-Duf-Cah-061411TLU]" value="Base-Royal-Athle-Duf-Cah-061411|TLU|1|100|105|15|-105" onclick="checkPoint(this.name)"></td>

			</tr>

			<!-- Pending / Previous row -->
			

			<tr><td class="trLine" colspan=9></td></tr>

		
			
			
			
			
			

			<input name="events[Base-Reds(-Dodge-Cue-Ker-061411]" id="events[Base-Reds(-Dodge-Cue-Ker-061411]" type="hidden" value="Base-Reds(-Dodge-Cue-Ker-061411^Dodgers(LA)^Reds(Cincinnati)^06-14-11 10:10 PM^06/14/11^10:10 PM^204" />
			

			<tr valign=middle class="even">
            	<td class="left">
	            	06/14/11 
            	</td>

            	
				<td align="center" width="30">965</td>
				<td class="left"><a title="Team Statistics" href="#" onclick="javascript:window.open(\'http://www.sportsbook.com/statfeed/betting/statfeed.php?page=mlb/mlbteam&teamid=CINCINNATI\', \'statfox\'); return false;">Reds(Cincinnati)<br>Cueto</a></td>

				<td align="right"><a title="Click to bet on Reds(Cincinnati) to win straight up." target="TICKET" href="#" onclick="javascript:quickbet(getValueFromChkBox(\'selection[Base-Reds(-Dodge-Cue-Ker-061411MLA]\'),204, 1);return false; "><span id="Base-Reds(-Dodge-Cue-Ker-061411MLA">+118</span></a></td>

				<td ><input type="checkbox" name="selection[Base-Reds(-Dodge-Cue-Ker-061411MLA]" id="selection[Base-Reds(-Dodge-Cue-Ker-061411MLA]" value="Base-Reds(-Dodge-Cue-Ker-061411|MLA|1|118|100|0|+118" onclick="checkMoney(this.name)"></td>


				<td align="right"><a title="Click to bet on Reds(Cincinnati) to cover the spread." target="TICKET" href="#" onclick="javascript:quickbet(getValueFromChkBox(\'selection[Base-Reds(-Dodge-Cue-Ker-061411PSA]\'),204, 1);return false; "><span id="Base-Reds(-Dodge-Cue-Ker-061411PSA">+1.5 (-220)</span></a></td>

				<td ><input type="checkbox" name="selection[Base-Reds(-Dodge-Cue-Ker-061411PSA]" id="selection[Base-Reds(-Dodge-Cue-Ker-061411PSA]" value="Base-Reds(-Dodge-Cue-Ker-061411|PSA|1|100|220|3|-220" onclick="checkPoint(this.name)"></td>


				<td class="overUnder" align="right"><a title="Click to bet on the games total score to be over 6" target="TICKET" href="#" onclick="javascript:quickbet(getValueFromChkBox(\'selection[Base-Reds(-Dodge-Cue-Ker-061411TLO]\'),204, 1);return false; "><span id="Base-Reds(-Dodge-Cue-Ker-061411TLO">Over 6.5 (-115)</span></a></td>

				<td class="overUnder"><input type="checkbox" name="selection[Base-Reds(-Dodge-Cue-Ker-061411TLO]" id="selection[Base-Reds(-Dodge-Cue-Ker-061411TLO]" value="Base-Reds(-Dodge-Cue-Ker-061411|TLO|1|100|115|13|-115" onclick="checkPoint(this.name)"></td>


           </tr>

			<!-- Pending / Previous row -->

			

 			

			
				<tr align="center" class="even">
					<td class="left"><a href="#" onclick="javascript:window.open(\'http://www.sportsbook.com/statfeed/betting/statfeed.php?page=mlb/expanded&gameid=20110614LADODGERS\',\'statfox\'); return false;" >Matchup</a></td>
					
 					<td class="left"><a href="#" onclick="javascript:window.open(\'http://www.sportsbook.com/statfeed/betting/statfeed.php?page=mlb/trend&gameid=20110614LADODGERS\',\'statfox\'); return false;" >Trend Sheet</a></td> 
							
					<!--  Teamname and lines -->
					<td class="left"></td>
					<td></td> <td></td> 	<td></td><td></td> 	 <td class="overUnder"></td><td class="overUnder"></td>
				</tr>

			

			<tr valign=middle class="even">
				<td class="left">
				22:10 ET 
				</td>
				
				<td align="center" width="30">966</td>
				<td class="left"><a title="Team Statistics" href="#" onclick="javascript:window.open(\'http://www.sportsbook.com/statfeed/betting/statfeed.php?page=mlb/mlbteam&teamid=LA DODGERS\', \'statfox\'); return false;">Dodgers(LA)<br>Kershaw</a></td>

				<td align="right"><a title="Click to bet on Dodgers(LA) to win straight up." target="TICKET" href="#" onclick="javascript:quickbet(getValueFromChkBox(\'selection[Base-Reds(-Dodge-Cue-Ker-061411MLH]\'),204, 1);return false; "><span id="Base-Reds(-Dodge-Cue-Ker-061411MLH">-128</span></a></td>

				<td ><input type="checkbox" name="selection[Base-Reds(-Dodge-Cue-Ker-061411MLH]" id="selection[Base-Reds(-Dodge-Cue-Ker-061411MLH]" value="Base-Reds(-Dodge-Cue-Ker-061411|MLH|1|100|128|0|-128" onclick="checkMoney(this.name)"></td>


				<td align="right"><a title="Click to bet on Dodgers(LA) to cover the spread." target="TICKET" href="#" onclick="javascript:quickbet(getValueFromChkBox(\'selection[Base-Reds(-Dodge-Cue-Ker-061411PSH]\'),204, 1);return false; "><span id="Base-Reds(-Dodge-Cue-Ker-061411PSH">-1.5 (+180)</span></a></td>

				<td ><input type="checkbox" name="selection[Base-Reds(-Dodge-Cue-Ker-061411PSH]" id="selection[Base-Reds(-Dodge-Cue-Ker-061411PSH]" value="Base-Reds(-Dodge-Cue-Ker-061411|PSH|1|180|100|-3|+180" onclick="checkPoint(this.name)"></td>


				<td class="overUnder" align="right"><a title="Click to bet on the games total score to be under 6" target="TICKET" href="#" onclick="javascript:quickbet(getValueFromChkBox(\'selection[Base-Reds(-Dodge-Cue-Ker-061411TLU]\'),204, 1);return false; "><span id="Base-Reds(-Dodge-Cue-Ker-061411TLU">Under 6.5 (-105)</span></a></td>

				<td class="overUnder"><input type="checkbox" name="selection[Base-Reds(-Dodge-Cue-Ker-061411TLU]" id="selection[Base-Reds(-Dodge-Cue-Ker-061411TLU]" value="Base-Reds(-Dodge-Cue-Ker-061411|TLU|1|100|105|13|-105" onclick="checkPoint(this.name)"></td>


			</tr>

			<!-- Pending / Previous row -->
			

			<tr><td class="trLine" colspan=9></td></tr>

		
			
			
			
			
			

			<input name="events[Base-Angel-Marin-Jer-Fis-061411]" id="events[Base-Angel-Marin-Jer-Fis-061411]" type="hidden" value="Base-Angel-Marin-Jer-Fis-061411^Mariners(Seattle)^Angels(LAA)^06-14-11 10:10 PM^06/14/11^10:10 PM^204" />
			

			<tr valign=middle class="odd">
            	<td class="left">

	            	06/14/11 
            	</td>
            	
				<td align="center" width="30">979</td>
				<td class="left"><a title="Team Statistics" href="#" onclick="javascript:window.open(\'http://www.sportsbook.com/statfeed/betting/statfeed.php?page=mlb/mlbteam&teamid=LA ANGELS\', \'statfox\'); return false;">Angels(LAA)<br>JerWeaver</a></td>

				<td align="right"><a title="Click to bet on Angels(LAA) to win straight up." target="TICKET" href="#" onclick="javascript:quickbet(getValueFromChkBox(\'selection[Base-Angel-Marin-Jer-Fis-061411MLA]\'),204, 1);return false; "><span id="Base-Angel-Marin-Jer-Fis-061411MLA">-135</span></a></td>

				<td ><input type="checkbox" name="selection[Base-Angel-Marin-Jer-Fis-061411MLA]" id="selection[Base-Angel-Marin-Jer-Fis-061411MLA]" value="Base-Angel-Marin-Jer-Fis-061411|MLA|1|100|135|0|-135" onclick="checkMoney(this.name)"></td>


				<td align="right"><a title="Click to bet on Angels(LAA) to cover the spread." target="TICKET" href="#" onclick="javascript:quickbet(getValueFromChkBox(\'selection[Base-Angel-Marin-Jer-Fis-061411PSA]\'),204, 1);return false; "><span id="Base-Angel-Marin-Jer-Fis-061411PSA">-1.5 (+125)</span></a></td>

				<td ><input type="checkbox" name="selection[Base-Angel-Marin-Jer-Fis-061411PSA]" id="selection[Base-Angel-Marin-Jer-Fis-061411PSA]" value="Base-Angel-Marin-Jer-Fis-061411|PSA|1|125|100|-3|+125" onclick="checkPoint(this.name)"></td>


				<td class="overUnder" align="right"><a title="Click to bet on the games total score to be over 6" target="TICKET" href="#" onclick="javascript:quickbet(getValueFromChkBox(\'selection[Base-Angel-Marin-Jer-Fis-061411TLO]\'),204, 1);return false; "><span id="Base-Angel-Marin-Jer-Fis-061411TLO">Over 6.5 (-105)</span></a></td>

				<td class="overUnder"><input type="checkbox" name="selection[Base-Angel-Marin-Jer-Fis-061411TLO]" id="selection[Base-Angel-Marin-Jer-Fis-061411TLO]" value="Base-Angel-Marin-Jer-Fis-061411|TLO|1|100|105|13|-105" onclick="checkPoint(this.name)"></td>

           </tr>

			<!-- Pending / Previous row -->
			

 			

			
				<tr align="center" class="odd">
					<td class="left"><a href="#" onclick="javascript:window.open(\'http://www.sportsbook.com/statfeed/betting/statfeed.php?page=mlb/expanded&gameid=20110614SEATTLE\',\'statfox\'); return false;" >Matchup</a></td>
					
 					<td class="left"><a href="#" onclick="javascript:window.open(\'http://www.sportsbook.com/statfeed/betting/statfeed.php?page=mlb/trend&gameid=20110614SEATTLE\',\'statfox\'); return false;" >Trend Sheet</a></td> 
							
					<!--  Teamname and lines -->
					<td class="left"></td>

					<td></td> <td></td> 	<td></td><td></td> 	 <td class="overUnder"></td><td class="overUnder"></td>
				</tr>
			

			<tr valign=middle class="odd">
				<td class="left">
				22:10 ET 
				</td>
				
				<td align="center" width="30">980</td>

				<td class="left"><a title="Team Statistics" href="#" onclick="javascript:window.open(\'http://www.sportsbook.com/statfeed/betting/statfeed.php?page=mlb/mlbteam&teamid=SEATTLE\', \'statfox\'); return false;">Mariners(Seattle)<br>Fister</a></td>

				<td align="right"><a title="Click to bet on Mariners(Seattle) to win straight up." target="TICKET" href="#" onclick="javascript:quickbet(getValueFromChkBox(\'selection[Base-Angel-Marin-Jer-Fis-061411MLH]\'),204, 1);return false; "><span id="Base-Angel-Marin-Jer-Fis-061411MLH">+125</span></a></td>

				<td ><input type="checkbox" name="selection[Base-Angel-Marin-Jer-Fis-061411MLH]" id="selection[Base-Angel-Marin-Jer-Fis-061411MLH]" value="Base-Angel-Marin-Jer-Fis-061411|MLH|1|125|100|0|+125" onclick="checkMoney(this.name)"></td>


				<td align="right"><a title="Click to bet on Mariners(Seattle) to cover the spread." target="TICKET" href="#" onclick="javascript:quickbet(getValueFromChkBox(\'selection[Base-Angel-Marin-Jer-Fis-061411PSH]\'),204, 1);return false; "><span id="Base-Angel-Marin-Jer-Fis-061411PSH">+1.5 (-145)</span></a></td>

				<td ><input type="checkbox" name="selection[Base-Angel-Marin-Jer-Fis-061411PSH]" id="selection[Base-Angel-Marin-Jer-Fis-061411PSH]" value="Base-Angel-Marin-Jer-Fis-061411|PSH|1|100|145|3|-145" onclick="checkPoint(this.name)"></td>


				<td class="overUnder" align="right"><a title="Click to bet on the games total score to be under 6" target="TICKET" href="#" onclick="javascript:quickbet(getValueFromChkBox(\'selection[Base-Angel-Marin-Jer-Fis-061411TLU]\'),204, 1);return false; "><span id="Base-Angel-Marin-Jer-Fis-061411TLU">Under 6.5 (-115)</span></a></td>

				<td class="overUnder"><input type="checkbox" name="selection[Base-Angel-Marin-Jer-Fis-061411TLU]" id="selection[Base-Angel-Marin-Jer-Fis-061411TLU]" value="Base-Angel-Marin-Jer-Fis-061411|TLU|1|100|115|13|-115" onclick="checkPoint(this.name)"></td>


			</tr>

			<!-- Pending / Previous row -->

			

			<tr><td class="trLine" colspan=9></td></tr>

		
	</tbody>
</table>';
?>