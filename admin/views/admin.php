<?php
/**
 * Represents the view for the administration dashboard.
 *
 * This includes the header, options, and other information that should provide
 * The User Interface to the end user.
 *
 * @package   Social_Polling
 * @author    Your Name <email@example.com>
 * @license   GPL-2.0+
 * @link      http://example.com
 * @copyright 2014 Your Name or Company Name
 */
?>
<div class="wrap">

	<h2><?php echo esc_html( get_admin_page_title() ); ?></h2>
       
    <form method="post">
		<table class="form-table">
			<tbody>
            	         
            <tr>
                <th>
                	<label for="input-text">Download Format</label>
                </th>
                <td>
                	<select name="select">
                    	<option>CSV</option>
                        <?php /*?><option>Option 2</option>
                        <option>Option 3</option><?php */?>
                    </select>
                </td>
			</tr>
            
            
            	<?php //Download Button ?>
				<tr>
					<th>
						<?php /*?><label for="input-text">Download Data</label><?php */?>
					</th>
					<td>
						
                        
                        
                        <a class="button-primary"  href="<?php echo add_query_arg('download', 'social_polling_voting'); ?> ">Voting Information</a>
    					 <a  class="button-primary"  href="<?php echo add_query_arg('download', 'social_polling_comments'); ?> ">Comment Information</a>
                        
                        
                        
                        
					</td>
				</tr>
			
			</tbody>
		</table>
	</form>

    
<?php ob_start(); ?>
	<!-- @TODO: Provide markup for your options page here. -->
<form>
		<table class="form-table">
			<tbody>
   
            
            
				<tr>
					<th>
						<label for="input-text">Text input</label>
					</th>
					<td>
						<input type="text" name="input-text" placeholder="Text" /><br />
				
					</td>
				</tr>
				<tr>
					<th>
						<label for="input-text">Select</label>
					</th>
					<td>
						<select name="select">
							<option>Option 1</option>
							<option>Option 2</option>
							<option>Option 3</option>
						</select>
						<pre>
&lt;select name="select">
  &lt;option>Option 1&lt;/option>
  &lt;option>Option 2&lt;/option>
  &lt;option>Option 3&lt;/option>
&lt;/select>
						</pre>
					</td>
				</tr>
				<tr>
					<th>
						<label for="multi-select">Multiple Select</label>
					</th>
					<td>
						<select name="multi-select" multiple="multiple">
							<option>Option 1</option>
							<option>Option 2</option>
							<option>Option 3</option>
							<option>Option 4</option>
							<option>Option 5</option>
							<option>Option 6</option>
						</select>
						<pre>
&lt;select name="multi-select" multiple="multiple">
  &lt;option>Option 1&lt;/option>
  &lt;option>Option 2&lt;/option>
  &lt;option>Option 3&lt;/option>
  &lt;option>Option 4&lt;/option>
  &lt;option>Option 5&lt;/option>
  &lt;option>Option 6&lt;/option>
&lt;/select>
				</pre>
					</td>
				</tr>
				<tr>
					<th>
						<label for="radio-buttons">Radio Buttons</label>
					</th>
					<td>
						<input type="radio" name="radio-buttons" value="option-1"/> Option 1 <br />
						<input type="radio" name="radio-buttons" value="option-2"/> Option 2 <br />
						<input type="radio" name="radio-buttons" value="option-3"/> Option 3 <br />
						<input type="radio" name="radio-buttons" value="option-4"/> Option 4 <br />
						<pre>
&lt;input type="radio" name="radio-buttons" value="option-1" /> Option 1
&lt;input type="radio" name="radio-buttons" value="option-2" /> Option 2
&lt;input type="radio" name="radio-buttons" value="option-3" /> Option 3
&lt;input type="radio" name="radio-buttons" value="option-4" /> Option 4
						</pre>
					</td>
				</tr>
				<tr>
					<th>
						<label for="input-checkbox">Checkbox</label>
					</th>
					<td>
						<input type="checkbox" name="input-checkbox" /> Option 1<br />
						<pre>
&lt;input type="checkbox" name="input-checkbox"/> Option 1
						</pre>
					</td>
				</tr>
				<tr>
					<th>
						<label for="checkbox-array">Checkbox Array</label>
					</th>
					<td>
						<input type='checkbox' name='checkbox-array[]' value='option-1'> Option 1<br />
						<input type='checkbox' name='checkbox-array[]' value='option-2'> Option 2<br />
						<input type='checkbox' name='checkbox-array[]' value='option-3'> Option 3<br />
						<pre>
&lt;input type='checkbox' name='checkbox-array[]' value='option-1'> Option 1
&lt;input type='checkbox' name='checkbox-array[]' value='option-2'> Option 2
&lt;input type='checkbox' name='checkbox-array[]' value='option-3'> Option 3
						</pre>
					</td>
				</tr>
				<tr>
					<th>
						<label for="input-fieldset">Fieldset and <br />HTML5 Elements</label>
					</th>
					<td>
						<fieldset>
							<legend>Legend</legend>
							<input type="email" placeholder="Email" /> Email<br />
							<input type="search" placeholder="Search" /> Search<br />
							<input type="tel" placeholder="Telephone" /> Telephone<br />
							<input type="text" placeholder="Text" /> Text<br />
							<input type="url" placeholder="URL" /> URL<br />
						</fieldset>
						<pre>
&lt;fieldset>
  &lt;legend>Legend&lt;/legend>
  &lt;input type="email" placeholder="Email" /> Email
  &lt;input type="search" placeholder="Search" /> Search
  &lt;input type="tel" placeholder="Telephone" /> Telephone
  &lt;input type="text" placeholder="text" /> Text
  &lt;input type="url" placeholder="URL" /> URL
&lt;/fieldset>
						</pre>
					</td>
				</tr>
				<tr>
					<th>
						<label for="input-time">Time Elements</label>
					</th>
					<td>
						Date: <input name="input-date" type="date" /><br />
						Month: <input name="input-month" type="month" /> <br />
						Week: <input name="input-week" type="week" /><br />
						Time: <input name="input-time" type="time" /><br />
						Local Date and Time: <input name="input-datetime-local" type="datetime-local" />
						<pre>
Date: &lt;input name="input-date" type="date" />
Month: &lt;input name="input-month" type="month" />
Week: &lt;input name="input-week" type="week" />
Time: &lt;input name="input-time" type="time" />
Local Date and Time: &lt;input name="input-datetime-local" type="datetime-local" />
						</pre>
					</td>
				</tr>
				<tr>
					<th>
						<label for="input-time">Other Elements</label>
					</th>
					<td>
						Number: <input name="input-number" type="number" min="0" max="20" /><br />
						Range: <input name="input-range" type="range" /><br />
						Color: <input name="input-color" type="color" /><br />
						<pre>
Number: &lt;input name="input-number" type="number" min="0" max="20" />
Range: &lt;input name="input-range" type="range" />
Color: &lt;input name="input-color" type="color" />
						</pre>
					</td>
				</tr>
				<tr>
					<th>
						<label for="input-time">Buttons</label>
					</th>
					<td>
						<input type="submit" value="Submit Input" class="button" /><br /><br />
						<input type="button" value="Secondary Button" class="button-secondary" /><br /><br />
						<input type="button" value="Primary Button" class="button-primary" />
						<pre>
&lt;input type="submit" value="Submit Input" class="button" />
&lt;input type="button" value="Secondary Button" class="button-secondary" />
&lt;input type="button" value="Primary Button" class="button-primary" />
					</td>
				</tr>
			</tbody>
		</table>
	</form>
<?php ob_end_clean(); ?>


</div><?php //.wrap ?>
