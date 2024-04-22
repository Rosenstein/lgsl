<?php
  namespace tltneon\LGSL;
 /*----------------------------------------------------------------------------------------------------------\
 |                                                                                                            |
 |                      [ LIVE GAME SERVER LIST ] [ RICHARD PERRY FROM GREYCUBE.COM ]                       |
 |                                                                                                            |
 |    Released under the terms and conditions of the GNU General Public License Version 3 (http://gnu.org)    |
 |                                                                                                            |
 \-----------------------------------------------------------------------------------------------------------*/

  global $lgsl_config, $lgsl_zone_number;
  $lgsl_zone_number = $_GET['zone'] ?? null;
  $ip = $_GET['ip'] ?? null;
  $port = (int) ($_GET['port'] ?? 0);
  if (!isset($lgsl_zone_number) && !$ip && !$port) { exit("LGSL PROBLEM: $lgsl_zone_number NOT SET"); }
  $background = $_GET['bg'] ?? '#FFF';
  $font = $_GET['font'] ?? '#000';
  $link = $_GET['link'] ?? '#0000ED';

  require "lgsl_class.php";

  $zone_width = "{$lgsl_config['zone']['width']}px";
  $zone_grid  = $lgsl_config['grid'][$lgsl_zone_number] ?? 1;
  $zone_count = 0;
  $output = '';

  $request     = empty($lgsl_config['players'][$lgsl_zone_number]) ? "s" : "sp";
  if ($lgsl_zone_number) {
    $server_list = Database::getServersGroup(["request"=>$request, "zone"=>$lgsl_zone_number]);
  } else {
    $server_list = [new Server(["ip"=>$ip, "c_port"=>$port])];
    $server_list[0]->queryCached($request);
  }
  //$server_list = lgsl_sort_servers($server_list);

  if (!$server_list) {
    $output .= "<div style='margin:auto; text-align:center'>NO SERVERS IN ZONE {$lgsl_zone_number}</div>"; return;
  }

  $output .= "
	<link rel='stylesheet' href='other/_lgsl_locations.css' type='text/css' />
  <style>
    html {
      background-color: $background;
      color: $font
    }
    a {
      color: $link;
    }
    .sidebarserver * {
      scrollbar-width: thin;
      scrollbar-color: black gray;
    }

    .sidebarserver *::-webkit-scrollbar {
      height: 12px;
      width: 12px;
    }
    .sidebarserver *::-webkit-scrollbar-track {
      background: gray;
    }
    .sidebarserver *::-webkit-scrollbar-thumb {
      background-color: black;
      border-radius: 5px;
      border: 3px solid gray;
    }

    .sidebarserver img {
      border: none;
    }

    .marquee{
      width:100%;
      white-space:nowrap;
      overflow:hidden;
    }

    .marquee span.on {
      display:inline-block;
      animation: marquee 10s infinite linear alternate;
    }
    .sidebarserver table:hover .marquee span.on {
      animation: marquee 4s infinite linear alternate;
    }

    @keyframes marquee{
      0%{transform: translateX(15px);}
      100%{transform: translateX(calc(-100% + {$zone_width} - 15px));}
    }
  </style>
  <table cellpadding='0' cellspacing='0' style='width:100%; margin:auto; text-align:center' class='sidebarserver'>
    <tr>";

    foreach ($server_list as $server) {

      if ($zone_count != 0 && !($zone_count % $zone_grid)) {
        $output .= "
        </tr>
        <tr>";
      }

      $zone_count ++;

      $marquee = strlen($server->getName()) > 25 ? "class='on'" : "";
      $output .= "
      <td style='vertical-align:top; text-align:center'>

        <table style='width:{$zone_width}; margin:auto; text-align:center' cellpadding='0' cellspacing='2'>

          <tr>
            <td title='{$lgsl_config['text']['slk']}' style='padding:0px; text-align:center'>
              <div style='left:0px; right:0px; top:0px; bottom:0px; width:{$zone_width}; white-space:nowrap; overflow:hidden; text-align:center'>
                <a href='{$server->getConnectionLink()}' style='text-decoration:none'>
                  {$server->getAddress()}
                </a>
              </div>
            </td>
          </tr>

          <tr>
            <td title='{$server->getName()}' style='padding:0px; text-align:center'>
              <div class='marquee' style='left:0px; right:0px; top:0px; bottom:0px; width:{$zone_width}; white-space:nowrap; overflow:hidden; text-align:center'>
                <span {$marquee}>{$server->getName()}</span>
              </div>
            </td>
          </tr>

          <tr>
            <td style='padding:0px; text-align:center'>
              <div style='left:0px; right:0px; top:0px; bottom:0px; width:{$zone_width}; padding:0px; position:relative'>
                <a href='".LGSL::link($server->getIp(), $server->getConnectionPort())."' target='_blank'>
                  <img alt='' src='{$server->getMapImage()}'          title='{$lgsl_config['text']['vsd']}' style='vertical-align:middle; width: 100%; border-radius: 4px;' />
                  <img alt='' src='{$server->mapPasswordImage()}' title='{$lgsl_config['text']['vsd']}' style='position:absolute; z-index:2; bottom:2px; right:2px;' />
                  <img alt='' src='{$server->addUrlPath($server->getGameIcon())}'          title='{$server->getGameFormatted()}'     style='position:absolute; z-index:2; top:2px; left:2px; width: 24px; border-radius: 4px;' />
                  ";
                  if ($lgsl_config['locations'])
                  $output .= "
                  <img alt='' class='details_location_image flag f{$server->getLocation()}' title='{$server->getLocationFormatted()}'      style='position:absolute; z-index:2; top:2px; right:2px;' />
                  ";
                  $output .= "
                </a>
              </div>
            </td>
          </tr>

          <tr>
            <td title='{$server->getMap()}' style='padding:0px; text-align:center'>
              <div style='left:0px; right:0px; top:0px; bottom:0px; width:{$zone_width}; white-space:nowrap; overflow:hidden; text-align:center'>
                {$server->getMap()}
              </div>
            </td>
          </tr>";

        if ($server->getPlayersCount() /*&& isset($lgsl_config['players']) && isset($lgsl_config['players'][$lgsl_zone_number])*/) {
          $zone_height = $lgsl_config['zone']['line_size'] * ($server->getPlayersCount() + 1);
          $zone_height = $zone_height > $lgsl_config['zone']['height'] ? "{$lgsl_config['zone']['height']}px" : "{$zone_height}px";

          $output .= "
          <tr>
            <td style='border-radius: 4px;'>
              <span style='padding:1px; float:left'> {$lgsl_config['text']['zpl']} </span>
              <span style='padding:1px; float:right'> {$server->getPlayersCountFormatted()} </span>";
              $players = $server->getPlayersArray();
              if (count($players) > 0) {
                $output .= "<div style='left:0px; right:0px; top:0px; bottom:0px; width:{$zone_width}; height:{$zone_height}; border-top: 1px solid #8080807a; overflow: overlay; text-align:left'>";

                foreach ($players as $player) {
                  $output .= "
                  <div style='left:0px; right:0px; top:0px; bottom:0px; padding:1px; white-space:nowrap; overflow:hidden; text-align:left' title='{$player['name']}'> {$player['name']} </div>";
                }

                $output .= "</div";
              } else {
                $inner_width = $server->getPlayersPercent();
                $output .="
                <br />
                <div style='margin-top: 5px; border: 1px solid #555555; background-color: #222222; height: 4px;'>
                  <div style='width: $inner_width%; background-color: #ff8400; height: 4px;'></div>
                </div>";
              }
              $output .= "
            </td>
          </tr>";
        } else {
          $output .= "
          <tr>
            <td style='padding:0px; border:1px solid; border-radius: 4px;'>
              <span style='padding:1px; float:left'> {$lgsl_config['text']['zpl']} </span>
              <span style='padding:1px; float:right'> {$server->getPlayersCount()} </span>
            </td>
          </tr>";
        }

        $output .= "
        </table>
      </td>";
    }

    $output .= "
    </tr>
  </table>";

if ($lgsl_config['preloader']) {
  echo $output;
}