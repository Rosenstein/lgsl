<?php
  namespace tltneon\LGSL;

  /*----------------------------------------------------------------------------------------------------------\
  |                                                                                                            |
  |                      [ LIVE GAME SERVER LIST ] [ RICHARD PERRY FROM GREYCUBE.COM ]                         |
  |                                                                                                            |
  |    Released under the terms and conditions of the GNU General Public License Version 3 (http://gnu.org)    |
  |                                                                                                            |
  \-----------------------------------------------------------------------------------------------------------*/

//------------------------------------------------------------------------------------------------------------+

  require "lgsl_class.php";
  global $output, $server, $title;

//------------------------------------------------------------------------------------------------------------+
// GET THE SERVER DETAILS AND PREPARE IT FOR DISPLAY

  global $lgsl_server_id;
  if ($lgsl_config['preloader']) {
    $lgsl_server_id = $_GET["s"] ?? null;
  }
  $lgsl_server_ip = $_GET["ip"] ?? "";
  $lgsl_server_port = $_GET["port"] ?? "";

  $server = new Server(["ip" => $lgsl_server_ip, "c_port" => $lgsl_server_port, "id" => $lgsl_server_id]);
  $server->lgsl_cached_query();

  if ($server->isvalid()) {
    $title .= " | {$server->get_name()}";
    $fields = $server->sort_player_fields();
    //$server = lgsl_sort_players($server->get_players());
    //$server = lgsl_sort_extras($server->get_extras());
    //$server = lgsl_server_html($server);

  //------------------------------------------------------------------------------------------------------------+

    $output .= "
    <div style='margin:auto; text-align:center'>
      <div class='spacer'></div>";

  //------------------------------------------------------------------------------------------------------------+
  // SHOW THE STANDARD INFO

    $output .= "
      <div id='servername_{$server->get_status()}'> {$server->get_name()} </div>
      <div class='details_info'>
        <div class='details_info_column'>
          <a id='gamelink' href='{$server->get_software_link()}'>{$lgsl_config['text']['slk']}</a>
          <div class='details_info_row'>
            <div class='details_info_scolumn'>
              <div class='details_info_srow'>
                <div class='details_info_ceil'>{$lgsl_config['text']['sts']}:</div><div class='details_info_ceil'>{$lgsl_config['text'][$server->get_status()]}</div></div>
              <div class='details_info_srow'>
                <div class='details_info_ceil'>{$lgsl_config['text']['adr']}:</div><div class='details_info_ceil'>{$server->get_ip()}</div></div>
              <div class='details_info_srow'>
                <div class='details_info_ceil'>{$lgsl_config['text']['cpt']}:</div><div class='details_info_ceil'>{$server->get_c_port()}</div></div>
              <div class='details_info_srow'>
                <div class='details_info_ceil'>{$lgsl_config['text']['qpt']}:</div><div class='details_info_ceil'>{$server->get_q_port()}</div></div></div>
            <div class='details_info_scolumn'>
              <div class='details_info_srow'>
                <div class='details_info_ceil'>{$lgsl_config['text']['typ']}:</div><div class='details_info_ceil'>{$server->get_type()}</div></div>
              <div class='details_info_srow'>
                <div class='details_info_ceil'>{$lgsl_config['text']['gme']}:</div><div class='details_info_ceil'>{$server->get_game()}</div></div>
              <div class='details_info_srow'>
                <div class='details_info_ceil'>{$lgsl_config['text']['map']}:</div><div class='details_info_ceil'>{$server->get_map()}</div></div>
              <div class='details_info_srow'>
                <div class='details_info_ceil'>{$lgsl_config['text']['plr']}:</div><div class='details_info_ceil'>{$server->get_players_count()}</div></div>
            </div>
          </div>
          <div class='details_info_row'>
            <div class='details_info_scolumn'>
              <div class='details_info_srow'>
                <div class='details_info_ceil'>Mode:</div><div class='details_info_ceil'>{$server->get_mode()}</div></div>
                <div class='details_info_srow'>
                  <div class='details_info_ceil'>{$lgsl_config['text']['lst']}:</div><div class='details_info_ceil'>{$server->get_timestamp()}</div></div></div>
             
          </div>
        </div>
        <div class='details_info_column zone{$server->get_zone()}' style='background-image: url({$server->get_map_image()});'>
          <i class='details_password_image zone{$server->get_zone()}' style='background-image: url({$server->map_password_image()});' title='{$lgsl_config['text']['map']}: {$server->get_map()}'></i>
					<i class='details_location_image flag f{$server->getLocation()}' title='{$server->location_text()}'></i>
          <i class='details_game_image' style='background-image: url({$server->add_url_path($server->game_icon())});' title='{$server->text_type_game()}'></i>
        </div>
      </div>";

  //------------------------------------------------------------------------------------------------------------+

    $output .= "<div class='spacer'></div>";

  //------------------------------------------------------------------------------------------------------------+

    $g = "ip={$server->get_ip()}&port={$server->get_c_port()}";
    if ($lgsl_config['history']) {
      //print_r($server->get_history());
      $output .= "<div style='overflow-x: auto;'><img src='charts.php?{$g}' alt='{$server->get_name()}' style='border-radius: 6px;' id='chart' /></div>";
    }

		$p = str_replace('src/', '', lgsl_url_path()) . ($lgsl_config["direct_index"] ? 'index.php' : '');
		$framespace = max(0, min(6, $server->get_players_count('active'))) * 8.8;
		$output .= "
        <details>
          <summary style='margin-bottom: 12px;'>
            {$lgsl_config['text']['cts']}
          </summary>
          <div>";
					
					if ($lgsl_config['image_mod']) {
						if (extension_loaded('gd')) {
							$output .= "
							<div style='overflow-x: auto;'><img src='userbar.php?{$g}' alt='{$server->get_name()}'/></div>
							<textarea onClick='this.select();'>[url={$p}?{$g}][img]{$p}userbar.php?{$g}[/img][/url]</textarea><br /><br />

							<div style='overflow-x: auto;'><img src='userbar.php?{$g}&t=2' alt='{$server->get_name()}'/></div>
							<textarea onClick='this.select();'>[url={$p}?{$g}][img]{$p}userbar.php?{$g}&t=2[/img][/url]</textarea><br /><br />

							<img src='userbar.php?{$g}&t=3' alt='{$server->get_name()}'/><br />
							<textarea onClick='this.select();'>[url={$p}?{$g}][img]{$p}userbar.php?{$g}&t=3[/img][/url]</textarea>
							";
						} else {
							$output .= "<div id='invalid_server_id'> Error while trying to display image userbar: GD library not loaded (see php.ini) </div>";
						}
					}
					
					$output .= "
						<iframe src='src/lgsl_zone.php?{$g}' alt='{$server->get_name()}' style='border: 0; display: block; background: white;width: 200px;height: calc(275px + {$framespace}px);margin: auto;'></iframe><br />
            <textarea onClick='this.select();'><iframe src='{$p}src/lgsl_zone.php?{$g}'></iframe></textarea>
          </div>
        </details>
        <div class='spacer'></div>
        <style>
          textarea {
            width: 32em;
            height: 2.3em;
            word-break: break-all;
          }
          @media (max-width: 414px){
            textarea { width: 98.5% !important; }
          }
          details[open] div {
            animation: spoiler 1s;
          }
          @keyframes spoiler {
            0%   {opacity: 0;}
            100% {opacity: 1;}
          }
        </style>";

  //------------------------------------------------------------------------------------------------------------+
  // SHOW THE PLAYERS

    $output .= "
    <div id='details_playerlist'>";

    if ($server->get_players_count('active') == 0 || count($server->get_players()) == 0) {
      $output .= "<div class='noinfo'>{$lgsl_config['text']['npi']}</div>";
    } else {
      $players = $server->get_players();
      $output .= "
      <table class='players_table'>
        <thead>
          <tr class='table_head'>";

        foreach ($fields as $field) {
          $field = ucfirst($lgsl_config['text'][substr(strtolower($field), 0, 3)]);
          $output .= "<th> {$field} </th>";
        }

        $output .= "
          </tr>
        </thead>
        <tbody>";

        foreach ($players as $player_key => $player) {
          $output .= "
          <tr>";

          foreach ($fields as $field) {
            $output .= "<td> {$player[$field]} </td>";
          }

          $output .= "
          </tr>";
        }

      $output .= "
        </tbody>
      </table>";
    }

    $output .= "
    </div>";

  //------------------------------------------------------------------------------------------------------------+

    $output .= "<div class='spacer'></div>";

  //------------------------------------------------------------------------------------------------------------+
  // SHOW THE SETTINGS

    if (count($server->get_extras()) == 0) {
      $output .= "<div class='noinfo'>{$lgsl_config['text']['nei']} </div>";
    } else {
      $extras = $server->get_extras();
      $hide_options = count($extras) > 40;
      if ($hide_options) {
         $output .= "
        <details>
          <summary style='margin-bottom: 12px;'>
            {$lgsl_config['text']['ctb']}
          </summary>
          <div>
         ";
      }
      $output .= "
      <table class='settings_table'>
        <thead>
          <tr class='table_head'>
            <th> {$lgsl_config['text']['ehs']} </th>
            <th> {$lgsl_config['text']['ehv']} </th>
          </tr>
        </thead>
        <tbody>";

      foreach ($extras as $field => $value) {
        $value = preg_replace('/((https*:\/\/|https*:\/\/www\.|www\.)[\w\d\.\-\/=$?​]*)/i', "<a href='$1' target='_blank'>$1</a>", $value);
        $output .= "
          <tr>
            <td> {$field} </td>
            <td> {$value} </td>
          </tr>";
      }

      $output .= "
        </tbody>
      </table>";
      if ($hide_options) {
        $output .= "
        </div>
        </details>";
      }
    }

  //------------------------------------------------------------------------------------------------------------+

    $output .= "<div class='spacer'></div>";

    $output .= "
    </div>";
  }
  else {
    $output .= "<div id='invalid_server_id'> {$lgsl_config['text']['mid']} </div>";
  }

//--------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------+
//------ PLEASE MAKE A DONATION OR SIGN THE GUESTBOOK AT GREYCUBE.COM IF YOU REMOVE THIS CREDIT ----------------------------------------------------------------------------------------------------+
  $output .= "<div style='text-align:center; font-family:tahoma; font-size:9px; padding: 33px 0px 11px 0px;'><a href='https://github.com/tltneon/lgsl' style='text-decoration:none'>".lgsl_version()."</a></div>";
//--------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------+

if ($lgsl_config['preloader']) {
  echo $output;
}