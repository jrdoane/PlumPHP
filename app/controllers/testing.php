<?php
/**
 * PlumPHP Welcome Controller
 *
 * PlumPHP is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 *
 * PlumPHP is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with PlumPHP.  If not, see <http://www.gnu.org/licenses/>.
 */


/**
 * ********** WARNING!!! **********
 * Controller is strictly for testing purposes. This should be deleted or 
 * revoked access in production use.
 * ********************************
 */
class Testing extends \Plum\Controller {
    public function index() {
        $html = new \Plum\HtmlBuilder();
        $html->tag('div', array(), '', true)->tag('div', array(), '', true)->tag('div', array(), '', true);
        echo $html->get_string();
    }

    /**
     * Lets try parsing some CSV, shall we?
     */
    public function csv() {
        $html = new \Plum\HtmlBuilder();
        $html->head()
            ->step_out('html')
            ->body();

        $csv = \Plum\HTTP::input('csvfile', \Plum\PARAM_RAW, \Plum\FROM_FILE);
        if(!empty($csv)) {
            $file = file_get_contents($csv['tmp_name']);
            unlink($csv['tmp_name']);

            $data = preg_replace("/\n/", ' ', $file);
            $data = preg_split('/(,,,,)|((?=[^,]")[\s]*(?<="[^,]))/', $data);

            $csv = array();
            foreach($data as $d) {
                $tr = preg_split("/[\"],[\"]/", $d);
                if(empty($tr)) {
                    continue;
                }
                foreach($tr as &$r) {
                    $r = trim($r);
                    $r = trim($r, '"');
                }
                $csv[] = $tr;
            }

            $title = array_shift($csv);
            $title = trim(array_shift($title));

            $fields = array_shift($csv);

            $ignored_items = 0;
            $insert_count = 0;
            $insert_data = array();
            $db = \Plum\DB::get_conn();
            foreach($csv as $row) {
                if(count($row) < 7) {
                    continue;
                }


                $tmp = array(
                    'date' => strtotime($row[0]),
                    'amount' => preg_replace('/,/', '', $row[1]),
                    'fee' => $row[2],
                    'interest' => $row[3],
                    'draft'     => empty($row[4]) ? '0.0' : $row[4],
                    'balance' => preg_replace('/,/', '', $row[5]),
                    'description' => $row[6],
                    'account_name' => $title
                );
                $md5str = '';
                foreach($tmp as $item) {
                    $md5str .= $item;
                }
                $tmp['checksum'] = md5($md5str);
                $result = $db->select('bank_transactions', array('checksum' => $tmp['checksum']));
                if($result->has_next()) {
                    $ignored_items++;
                    continue;
                }
                $insert_data[] = $tmp;
                $insert_count++;
            }

            $rval = $db->insert('bank_transactions', $insert_data);
            $html->h(3, "Successfully uploaded CSV data to the database.");
            $html->p("Uploaded {$insert_count} records, ignored {$ignored_items} that already existed.");
        }

        $attr = array(
            'name' => 'csvform',
            'enctype' => 'multipart/form-data',
            'action' => \Plum\Uri::href('testing/csv'),
            'method' => 'POST'
        );

        $html->form($attr)
            ->input('csvfile', 'file')->br()
            ->input('submit', 'submit', array('value' => 'Upload'))
            ->step_out('html');
        print $html->get_string();
    }
}
