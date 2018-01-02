<?php

/**
 * gross procedural code to get data from csv (from google docs recording account)
 * then process it for plain HTML output on Recording.LA checklists (for engineers)
 * with NASTY global variables
 */

 /* USAGE:

 wherever/prepare_checklist.php
 wherever/input.csv

 terminal:
 ~ cd wherever/   # this is very important.
 ~ php prepare_checklist.php

 wherever/generated-output.html   <--- this gets written

  */

$csv_src = "input.csv";

$output = "";

$html_tags = array(
    "section" => "h3",
);

function checkbox($id){
    return "<input type='checkbox' id='$id' value='' />";
}

$prevSection = null;
$ul1_is_open = false;
$ul2_is_open = false;
$ul3_is_open = false;


// do it
$counter = 1;
if (($handle = fopen($csv_src, "r")) !== FALSE) {

    while (($data = fgetcsv($handle, 1000, ",")) !== FALSE) {
        list($url,$section,$section_info,$ul1,$ul2,$ul3) = $data;

        // skip the first row!
        if($section == "Section")
            continue;
        else if($url == "Relative URL")
            continue;

        $counter++;

        if($section){
            ensure_all_closed();
            $tag = $html_tags['section'];
            $output .= "<$tag>$section</$tag>\n";
            if($section_info)
                $output .= "<p>$section_info</p>\n";            
            $prevSection = $section;
        }

        $id = "checklistbox$counter";
        $label_open = "<label for='$id'>";
        $label_close = "</label>";
        $link = $url ? "<br /><a href='$url' class='use-ajax' style='padding-left:6px;font-style:italic;' data-dialog-type='modal' data-dialog-options=\"{&quot;width&quot;:800,&quot;height&quot;:500}\">&raquo; More Info</a>" : "";

        $checkbox = checkbox($id);

        if($ul1){
            ensure_ul1_open();
            ensure_ul2_closed();
            ensure_ul3_closed();
            $output .= "\t<li>$checkbox $label_open$ul1$label_close$link</li>\n";
        }
        if($ul2){
            ensure_ul1_open();
            ensure_ul2_open();
            ensure_ul3_closed();
            $output .= "\t\t<li>$checkbox $label_open$ul2$label_close$link</li>\n";
        }
        if($ul3){
            ensure_ul1_open();
            ensure_ul2_open();
            ensure_ul3_open();
            $output .= "\t\t\t<li>$checkbox $label_open$ul3$label_close$link</li>\n";
        }

        #        $num = count($data);
        /*
        # echo "<p> $num fields in line $row: <br /></p>\n";
        $row++;
        for ($c=0; $c < $num; $c++) {
            # echo $data[$c] . "<br />\n";
        }
        //*/

        $prevRow = $data;
    }
    fclose($handle);
}

echo $output;
file_put_contents("generated-output.html",$output);


function ensure_ul1_open(){
    global $output, $ul1_is_open,$ul2_is_open,$ul3_is_open;
    if(!$ul1_is_open){
        $output .= "<ul>\n";
        $ul1_is_open = true;
    }
}

function ensure_ul2_open(){
    global $output, $ul1_is_open,$ul2_is_open,$ul3_is_open;
    if(!$ul2_is_open){
        $output .= "<ul>\n";
        $ul2_is_open = true;
    }
}


function ensure_ul3_open(){
    global $output, $ul1_is_open,$ul2_is_open,$ul3_is_open;
    if(!$ul3_is_open){
        $output .= "<ul>\n";
        $ul3_is_open = true;
    }
}

function ensure_all_closed(){
    ensure_ul1_closed();
    ensure_ul2_closed();
    ensure_ul3_closed();
}


function ensure_ul1_closed(){
    global $output, $ul1_is_open,$ul2_is_open,$ul3_is_open;
    if($ul1_is_open)
        $output .= "</ul>\n";
    $ul1_is_open = false;
}

function ensure_ul2_closed(){
    global $output, $ul1_is_open,$ul2_is_open,$ul3_is_open;
    if($ul2_is_open)
        $output .= "</ul>\n";
    $ul2_is_open = false;
}

function ensure_ul3_closed(){
    global $output, $ul1_is_open,$ul2_is_open,$ul3_is_open;
    if($ul3_is_open)
        $output .= "</ul>\n";
    $ul3_is_open = false;
}
