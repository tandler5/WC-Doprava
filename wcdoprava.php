<?php
/**
 * Plugin Name: WooCommerce doprava
 * Description: Implementace českých kurýrních služeb (DPD Pickup, Česká pošta, WE|DO, GLS) do Woocomerce
 * Version: 1.1
 * Author: Tandler
 * Author URI: https://github.com/tandler5
 * Plugin URI: https://github.com/tandler5/WC-Doprava
 */
add_action( 'woocommerce_shipping_init', 'doprava_doprava_zasilkovna_init' );
add_filter( 'woocommerce_shipping_methods', 'doprava_doprava_zasilkovna' );
add_action( 'wp_footer', 'doprava_zasilkovna_scripts_checkout', 100 );
add_action( 'woocommerce_review_order_after_shipping', 'doprava_zasilkovna_zobrazit_pobocky' );
add_action( 'woocommerce_new_order_item', 'doprava_zasilkovna_ulozeni_pobocky', 10, 2 );
add_action( 'woocommerce_checkout_process', 'doprava_zasilkovna_overit_pobocku' );
////////////////POSTA/////////////////////////////////
add_action( 'woocommerce_shipping_init', 'doprava_doprava_posta_init' );
add_filter( 'woocommerce_shipping_methods', 'doprava_doprava_posta' );
add_action( 'wp_footer', 'doprava_posta_scripts_checkout', 100 );
add_action( 'woocommerce_review_order_after_shipping', 'doprava_posta_zobrazit_pobocky' );
add_action( 'woocommerce_new_order_item', 'doprava_posta_ulozeni_pobocky', 10, 2 );
add_action( 'woocommerce_checkout_process', 'doprava_posta_overit_pobocku' );
////////////////WE|DO/////////////////////////////////
add_action( 'woocommerce_shipping_init', 'doprava_doprava_wedo_init' );
add_filter( 'woocommerce_shipping_methods', 'doprava_doprava_wedo' );
add_action( 'wp_footer', 'doprava_wedo_scripts_checkout', 100 );
add_action( 'woocommerce_review_order_after_shipping', 'doprava_wedo_zobrazit_pobocky' );
add_action( 'woocommerce_new_order_item', 'doprava_wedo_ulozeni_pobocky', 10, 2 );
add_action( 'woocommerce_checkout_process', 'doprava_wedo_overit_pobocku' );
////////////////GLS/////////////////////////////////
add_action( 'woocommerce_shipping_init', 'doprava_doprava_gls_init' );
add_filter( 'woocommerce_shipping_methods', 'doprava_doprava_gls' );
add_action( 'wp_footer', 'doprava_gls_scripts_checkout', 100 );
add_action( 'woocommerce_review_order_after_shipping', 'doprava_gls_zobrazit_pobocky' );
add_action( 'woocommerce_new_order_item', 'doprava_gls_ulozeni_pobocky', 10, 2 );
add_action( 'woocommerce_checkout_process', 'doprava_gls_overit_pobocku' );

add_action( 'woocommerce_review_order_after_shipping', 'return_shipping_method' );

function doprava_doprava_zasilkovna_init() {
  if ( ! class_exists( 'WC_Shipping_doprava_Zasilkovna' ) ) {
    require_once plugin_dir_path( __FILE__ ) . '/components/class-dpd.php';
  }
}
function doprava_doprava_posta_init() {
  if ( ! class_exists( 'WC_Shipping_doprava_Posta' ) ) {
    require_once plugin_dir_path( __FILE__ ) . '/components/class-cp.php';
  }
}
function doprava_doprava_wedo_init() {
  if ( ! class_exists( 'WC_Shipping_doprava_wedo' ) ) {
    require_once plugin_dir_path( __FILE__ ) . '/components/class-wedo.php';
  }
}
function doprava_doprava_gls_init() {
  if ( ! class_exists( 'WC_Shipping_doprava_gls' ) ) {
    require_once plugin_dir_path( __FILE__ ) . '/components/class-gls.php';
  }
}
function doprava_doprava_posta( $methods ) {
  $methods['doprava_posta'] = 'WC_Shipping_doprava_Posta';
  return $methods;
}

function doprava_doprava_zasilkovna( $methods ) {
  $methods['doprava_zasilkovna'] = 'WC_Shipping_doprava_Zasilkovna';
  return $methods;
}
function doprava_doprava_wedo( $methods ) {
  $methods['doprava_wedo'] = 'WC_Shipping_doprava_wedo';
  return $methods;
}
function doprava_doprava_gls( $methods ) {
  $methods['doprava_gls'] = 'WC_Shipping_doprava_gls';
  return $methods;
}
function return_shipping_method(){
    $chosen_shipping_method = WC()->session->get( 'chosen_shipping_methods' );
    if(strpos( $chosen_shipping_method[0], "doprava_zasilkovna" ) === false&& strpos( $chosen_shipping_method[0], "doprava_posta" ) === false&& strpos( $chosen_shipping_method[0], "doprava_wedo" ) === false&& strpos( $chosen_shipping_method[0], "doprava_gls" ) === false){
        ?><script>document.getElementById("customer_details").getElementsByClassName("woocommerce-shipping-fields")[0].removeAttribute("style", "display:none;");
        document.getElementById("ship-to-different-address-checkbox").checked = false;
        document.getElementById("shipping_first_name").value = "";
        document.getElementById("shipping_last_name").value = "";
        document.getElementById("shipping_company").value = "";
        document.getElementById("shipping_postcode").value = "";
        document.getElementById("shipping_address_1").value = "";
        document.getElementById("shipping_address_2").value = "";
        document.getElementById("shipping_city").value = "";
        </script><?php
    }
    else{
         ?><script>document.getElementById("customer_details").getElementsByClassName("woocommerce-shipping-fields")[0].setAttribute("style", "display:none;");</script><?php
    }
}

function doprava_zasilkovna_zobrazit_pobocky() {
  if ( is_ajax() ) {
    $zasilkovna_branches = '';
    if ( isset( $_POST['post_data'] ) ) {
      parse_str( $_POST['post_data'], $post_data );
      if ( isset( $post_data['packeta-point-id'] ) ) {
        $zasilkovna_branches = $post_data['packeta-point-id'];
      }
    }
    $chosen_shipping_method = WC()->session->get( 'chosen_shipping_methods' );
    if ( strpos( $chosen_shipping_method[0], "doprava_zasilkovna" ) !== false ) { ?>
      <tr class="zasilkovna">
        <th>
          <img src="/wp-content/plugins/wcdoprava/loga/DPD_logo_redgrad_rgb-300x133.png" width="200" border="0">
        </th>
        <td>
            <input type="hidden" id="packeta-point-id" name="packeta-point-id" value="<?php echo $zasilkovna_branches; ?>">
          <input type="button" onclick="Packeta.Widget.pick(packetaApiKey, showSelectedPickupPoint)" value="Zvolit pobočku">
        </td>
      </tr>
      <tr><th>Vybraná pobočka:</th><td><span id="packeta-point-info" style="font-weight:bold;"><?php if ( $zasilkovna_branches ) { echo $zasilkovna_branches; } else { echo "Zatím nevybráno"; } ?></span></td></tr>
    <?php } else { ?>
    <?php }
  }
}

function doprava_zasilkovna_ulozeni_pobocky( $item_id, $item ) {
  if ( isset( $_POST["packeta-point-id"] ) ) {
    if ( ! empty( $_POST["packeta-point-id"] ) && strpos( $_POST["shipping_method"][0], "doprava_zasilkovna" ) !== false ) {
      if ( version_compare( WC_VERSION, '3.0', '<' ) ) {
        $item_type = $item['order_item_type'];
      } else {
        $item_type = $item->get_type();
      }
      if ( $item_type == 'shipping' ) {
        wc_add_order_item_meta( $item_id, 'DPD Pickup', esc_attr( $_POST['packeta-point-id'] ), true );
      }
    }
  }
}

function doprava_zasilkovna_overit_pobocku() {
  if ( isset( $_POST["packeta-point-id"] ) ) {
    if (( empty( $_POST["packeta-point-id"])||$_POST["packeta-point-id"]==="undefined") && strpos( $_POST["shipping_method"][0], "doprava_zasilkovna" ) !== false ) {
      wc_add_notice( 'Pokud chcete doručit zboží prostřednictvím DPD Pickup, zvolte prosím pobočku.', 'error' );
    }
  }
}

function doprava_zasilkovna_objednavka_zobrazit_pobocku( $order ) {
  if ( $order->has_shipping_method( 'doprava_zasilkovna' ) ) {
    foreach ( $order->get_shipping_methods() as $shipping_item_id => $shipping_item ) {
      if ( version_compare( WC_VERSION, '3.0', '<' ) ) {
        $pobocka = $order->get_item_meta( $shipping_item_id, 'DPD Pickup', true );
      } else {
        $pobocka = wc_get_order_item_meta( $shipping_item_id, 'DPD Pickup', true );
      }
      if ( ! empty( $pobocka ) ) {
        echo "<p><strong>DPD Pickup:</strong> " . $pobocka . "</p>";
      }
    }
  }
}

function doprava_zasilkovna_scripts_checkout() {
  if ( is_checkout() ) {
    $zasilkovna_settings = get_option( 'woocommerce_doprava_zasilkovna_settings' ); 
     $api_klic = $zasilkovna_settings['op1']; ?>
      <script src="/wp-content/plugins/wcdoprava/js/dpd.js"></script>
      <script type="text/javascript">
        var packetaApiKey = '<?php echo $api_klic; ?>';
        var $storage_support = true;
        try {
          $storage_support = ( 'sessionStorage' in window && window.sessionStorage !== null );
          window.localStorage.setItem( 'doprava', 'test' );
          window.localStorage.removeItem( 'doprava' );
        } catch( err ) {
          $storage_support = false;
        }
        if ( $storage_support ) {
          jQuery( document ).ready(function( $ ) {
            $( document.body ).on( 'updated_checkout', function() {
              var doprava_zasilkovna = localStorage.getItem( 'doprava_zasilkovna' );
              if ( document.getElementById( 'packeta-point-info' ) !== null ) {
                var paragraph = document.getElementById( 'packeta-point-info' ).firstChild;
                if ( doprava_zasilkovna !== null ) {
                  paragraph.nodeValue = doprava_zasilkovna;
                  document.getElementById( 'packeta-point-id' ).value = doprava_zasilkovna;
                } else if ( paragraph !== "Zatím nevybráno" ) {
                  paragraph.nodeValue = "Zatím nevybráno";
                }
              }
            })
          });
        }
        function showSelectedPickupPoint(point) {
          var spanElement = document.getElementById( 'packeta-point-info' );
          var idElement = document.getElementById( 'packeta-point-id' );
          if ( point ) {
            spanElement.innerText = point.name;
            idElement.value = point.name;
            if ( $storage_support ) {
              localStorage.setItem( 'doprava_zasilkovna', point.name );
            }
          }
          else {
            if ( $storage_support ) {
              var doprava_zasilkovna = localStorage.getItem( 'doprava_zasilkovna' );
            } else {
              var doprava_zasilkovna = null;
            }
            if ( doprava_zasilkovna !== null ) {
              spanElement.innerText = doprava_zasilkovna;
              idElement.value = doprava_zasilkovna;
            } else {
              spanElement.innerText = "Zatím nevybráno";
              idElement.value = "";
            }
          }
        };
      </script>
    <?php 
  }
}
///////////////////////////////////////////////////////////////////////////////////////////////////////////////
function doprava_posta_zobrazit_pobocky() {
  if ( is_ajax() ) {
    $posta_branches = '';
    if ( isset( $_POST['post_data'] ) ) {
      parse_str( $_POST['post_data'], $post_data );
      if ( isset( $post_data['packeta-point-id'] ) ) {
        $posta_branches = $post_data['packeta-point-id'];
      }
    }
    $chosen_shipping_method = WC()->session->get( 'chosen_shipping_methods' );
    if ( strpos( $chosen_shipping_method[0], "doprava_posta" ) !== false ) { ?>
      <tr class="posta">
        <th>
          <img src="/wp-content/plugins/wcdoprava/loga/cp.svg" width="200" border="0">
        </th>
        <td>
        <input type="hidden" id="packeta-point-id" name="packeta-point-id" value="<?php echo $zasilkovna_branches; ?>">
          <input type="button" onclick="Packetaa.Widget.pick(packetaApiKey,packetaApiKey2, showSelectedPickupPoint)" value="Zvolit pobočku">
        </td>
      </tr>
      <tr><th>Vybraná pobočka:</th><td><span id="packeta-point-info" style="font-weight:bold;"><?php if ( $posta_branches ) { echo $posta_branches; } else { echo "Zatím nevybráno"; } ?></span></td></tr>
    <?php } else { ?>
    <?php }
  }
}
function doprava_posta_ulozeni_pobocky( $item_id, $item ) {
  if ( isset( $_POST["packeta-point-id"] ) ) {
    if ( ! empty( $_POST["packeta-point-id"] ) && strpos( $_POST["shipping_method"][0], "doprava_posta" ) !== false ) {
      if ( version_compare( WC_VERSION, '3.0', '<' ) ) {
        $item_type = $item['order_item_type'];
      } else {
        $item_type = $item->get_type();
      }
      if ( $item_type == 'shipping' ) {
        wc_add_order_item_meta( $item_id, 'Česká pošta', esc_attr( $_POST['packeta-point-id'] ), true );
      }
    }
  }
}

function doprava_posta_overit_pobocku() {
  if ( isset( $_POST["packeta-point-id"] ) ) {
    if (( empty( $_POST["packeta-point-id"])||$_POST["packeta-point-id"]==="undefined") && strpos( $_POST["shipping_method"][0], "doprava_posta" ) !== false ) {
      wc_add_notice( 'Pokud chcete doručit zboží prostřednictvím České pošty, zvolte prosím pobočku.', 'error' );
    }
  }
}

function doprava_posta_objednavka_zobrazit_pobocku( $order ) {
  if ( $order->has_shipping_method( 'doprava_posta' ) ) {
    foreach ( $order->get_shipping_methods() as $shipping_item_id => $shipping_item ) {
      if ( version_compare( WC_VERSION, '3.0', '<' ) ) {
        $pobocka = $order->get_item_meta( $shipping_item_id, 'Česká pošta', true );
      } else {
        $pobocka = wc_get_order_item_meta( $shipping_item_id, 'Česká pošta', true );
      }
      if ( ! empty( $pobocka ) ) {
        echo "<p><strong>Česká pošta:</strong> " . $pobocka . "</p>";
      }
    }
  }
}
function doprava_posta_scripts_checkout() {
  if ( is_checkout() ) {
    $posta_settings = get_option( 'woocommerce_doprava_posta_settings' ); 
     $api_klic = $posta_settings['op1'];
     $api_klic2 = $posta_settings['op2']; ?>
      <script src="/wp-content/plugins/wcdoprava/js/cp.js"></script>
      <script type="text/javascript">
        var packetaApiKey = '<?php echo $api_klic; ?>';
        var packetaApiKey2= '<?php echo $api_klic2; ?>';
        var $storage_support = true;
        try {
          $storage_support = ( 'sessionStorage' in window && window.sessionStorage !== null );
          window.localStorage.setItem( 'doprava', 'test' );
          window.localStorage.removeItem( 'doprava' );
        } catch( err ) {
          $storage_support = false;
        }
        if ( $storage_support ) {
          jQuery( document ).ready(function( $ ) {
            $( document.body ).on( 'updated_checkout', function() {
              var doprava_posta = localStorage.getItem( 'doprava_posta' );
              if ( document.getElementById( 'packeta-point-info' ) !== null ) {
                var paragraph = document.getElementById( 'packeta-point-info' ).firstChild;
                if ( doprava_posta !== null ) {
                  paragraph.nodeValue = doprava_posta;
                  document.getElementById( 'packeta-point-id' ).value = doprava_posta;
                } else if ( paragraph !== "Zatím nevybráno" ) {
                  paragraph.nodeValue = "Zatím nevybráno";
                }
              }
            })
          });
        }
        function showSelectedPickupPoint(point) {
          var spanElement = document.getElementById( 'packeta-point-info' );
          var idElement = document.getElementById( 'packeta-point-id' );
          if ( point ) {
            spanElement.innerText = point.name;
            idElement.value = point.name;
            if ( $storage_support ) {
              localStorage.setItem( 'doprava_posta', point.name );
            }
          }
          else {
            if ( $storage_support ) {
              var doprava_posta = localStorage.getItem( 'doprava_posta' );
            } else {
              var doprava_posta = null;
            }
            if ( doprava_posta !== null ) {
              spanElement.innerText = doprava_posta;
              idElement.value = doprava_posta;
            } else {
              spanElement.innerText = "Zatím nevybráno";
              idElement.value = "";
            }
          }
        };
      </script>
    <?php 
  }
}
///////////////////////////////////////////////////////////////////////////////////////////////////////////////
function doprava_wedo_zobrazit_pobocky() {
  if ( is_ajax() ) {
    $wedo_branches = '';
    if ( isset( $_POST['post_data'] ) ) {
      parse_str( $_POST['post_data'], $post_data );
      if ( isset( $post_data['packeta-point-id'] ) ) {
        $wedo_branches = $post_data['packeta-point-id'];
      }
    }
    $chosen_shipping_method = WC()->session->get( 'chosen_shipping_methods' );
    if ( strpos( $chosen_shipping_method[0], "doprava_wedo" ) !== false ) { ?>
      <tr class="wedo">
        <th>
          <img src="/wp-content/plugins/wcdoprava/loga/WE_DO_na_bile_RGB.png" width="200" border="0">
        </th>
        <td>
            <input type="hidden" id="packeta-point-id" name="packeta-point-id" value="<?php echo $zasilkovna_branches; ?>">
          <input type="button" onclick="Packetaaaa.Widget.pick(packetaApiKey,packetaApiKey2, showSelectedPickupPoint)" value="Zvolit pobočku">
        </td>
      </tr>
      <tr><th>Vybraná pobočka:</th><td><span id="packeta-point-info" style="font-weight:bold;"><?php if ( $wedo_branches ) { echo $wedo_branches; } else { echo "Zatím nevybráno"; } ?></span></td></tr>
    <?php } else { ?>
    <?php }
  }
}

function doprava_wedo_ulozeni_pobocky( $item_id, $item ) {
  if ( isset( $_POST["packeta-point-id"] ) ) {
    if ( ! empty( $_POST["packeta-point-id"] ) && strpos( $_POST["shipping_method"][0], "doprava_wedo" ) !== false ) {
      if ( version_compare( WC_VERSION, '3.0', '<' ) ) {
        $item_type = $item['order_item_type'];
      } else {
        $item_type = $item->get_type();
      }
      if ( $item_type == 'shipping' ) {
        wc_add_order_item_meta( $item_id, 'WE|DO', esc_attr( $_POST['packeta-point-id'] ), true );
      }
    }
  }
}

function doprava_wedo_overit_pobocku() {
  if ( isset( $_POST["packeta-point-id"] ) ) {
    if (( empty( $_POST["packeta-point-id"])||$_POST["packeta-point-id"]==="undefined") && strpos( $_POST["shipping_method"][0], "doprava_wedo" ) !== false ) {
      wc_add_notice( 'Pokud chcete doručit zboží prostřednictvím České pošty, zvolte prosím pobočku.', 'error' );
    }
  }
}

function doprava_wedo_objednavka_zobrazit_pobocku( $order ) {
  if ( $order->has_shipping_method( 'doprava_wedo' ) ) {
    foreach ( $order->get_shipping_methods() as $shipping_item_id => $shipping_item ) {
      if ( version_compare( WC_VERSION, '3.0', '<' ) ) {
        $pobocka = $order->get_item_meta( $shipping_item_id, 'WE|DO', true );
      } else {
        $pobocka = wc_get_order_item_meta( $shipping_item_id, 'WE|DO', true );
      }
      if ( ! empty( $pobocka ) ) {
        echo "<p><strong>WE|DO:</strong> " . $pobocka . "</p>";
      }
    }
  }
}

function doprava_wedo_scripts_checkout() {
  if ( is_checkout() ) {
    $wedo_settings = get_option( 'woocommerce_doprava_wedo_settings' ); 
     $api_klic = $wedo_settings['op1'];
     $api_klic2 = $wedo_settings['op2']; ?>
      <script src="/wp-content/plugins/wcdoprava/js/wedo.js"></script>
      <script type="text/javascript">
        var packetaApiKey = '<?php echo $api_klic; ?>';
        var packetaApiKey2= '<?php echo $api_klic2; ?>';
        var $storage_support = true;
        try {
          $storage_support = ( 'sessionStorage' in window && window.sessionStorage !== null );
          window.localStorage.setItem( 'doprava', 'test' );
          window.localStorage.removeItem( 'doprava' );
        } catch( err ) {
          $storage_support = false;
        }
        if ( $storage_support ) {
          jQuery( document ).ready(function( $ ) {
            $( document.body ).on( 'updated_checkout', function() {
              var doprava_wedo = localStorage.getItem( 'doprava_wedo' );
              if ( document.getElementById( 'packeta-point-info' ) !== null ) {
                var paragraph = document.getElementById( 'packeta-point-info' ).firstChild;
                if ( doprava_wedo !== null ) {
                  paragraph.nodeValue = doprava_wedo;
                  document.getElementById( 'packeta-point-id' ).value = doprava_wedo;
                } else if ( paragraph !== "Zatím nevybráno" ) {
                  paragraph.nodeValue = "Zatím nevybráno";
                }
              }
            })
          });
        }
        function showSelectedPickupPoint(point) {
          var spanElement = document.getElementById( 'packeta-point-info' );
          var idElement = document.getElementById( 'packeta-point-id' );
          if ( point ) {
            spanElement.innerText = point.name;
            idElement.value = point.name;
            if ( $storage_support ) {
              localStorage.setItem( 'doprava_wedo', point.name );
            }
          }
          else {
            if ( $storage_support ) {
              var doprava_wedo = localStorage.getItem( 'doprava_wedo' );
            } else {
              var doprava_wedo = null;
            }
            if ( doprava_wedo !== null ) {
              spanElement.innerText = doprava_wedo;
              idElement.value = doprava_wedo;
            } else {
              spanElement.innerText = "Zatím nevybráno";
              idElement.value = "";
            }
          }
        };
      </script>
    <?php 
  }
}
///////////////////////////////////////////////////////////////////////////////////////////////////////////////
function doprava_gls_zobrazit_pobocky() {
  if ( is_ajax() ) {
    $gls_branches = '';
    if ( isset( $_POST['post_data'] ) ) {
      parse_str( $_POST['post_data'], $post_data );
      if ( isset( $post_data['packeta-point-id'] ) ) {
        $gls_branches = $post_data['packeta-point-id'];
      }
    }
    $chosen_shipping_method = WC()->session->get( 'chosen_shipping_methods' );
    if ( strpos( $chosen_shipping_method[0], "doprava_gls" ) !== false ) { ?>
      <tr class="gls">
        <th>
          <img src="/wp-content/plugins/wcdoprava/loga/GLS_Logo_2021_RGB_GLSBlue.png" width="200" border="0">
        </th>
        <td>
         <input type="hidden" id="packeta-point-id" name="packeta-point-id" value="<?php echo $zasilkovna_branches; ?>">
          <input type="button" onclick="Packetaaaaa.Widget.pick(packetaApiKey,packetaApiKey2, showSelectedPickupPoint)" value="Zvolit pobočku">
        </td>
      </tr>
      <tr><th>Vybraná pobočka:</th><td><span id="packeta-point-info" style="font-weight:bold;"><?php if ( $gls_branches ) { echo $wedo_branches; } else { echo "Zatím nevybráno"; } ?></span></td></tr>
    <?php } else { ?>
    <?php }
  }
}

function doprava_gls_ulozeni_pobocky( $item_id, $item ) {
  if ( isset( $_POST["packeta-point-id"] ) ) {
    if ( ! empty( $_POST["packeta-point-id"] ) && strpos( $_POST["shipping_method"][0], "doprava_gls" ) !== false ) {
      if ( version_compare( WC_VERSION, '3.0', '<' ) ) {
        $item_type = $item['order_item_type'];
      } else {
        $item_type = $item->get_type();
      }
      if ( $item_type == 'shipping' ) {
        wc_add_order_item_meta( $item_id, 'GLS', esc_attr( $_POST['packeta-point-id'] ), true );
      }
    }
  }
}

function doprava_gls_overit_pobocku() {
  if ( isset( $_POST["packeta-point-id"] ) ) {
    if (( empty( $_POST["packeta-point-id"])||$_POST["packeta-point-id"]==="undefined") && strpos( $_POST["shipping_method"][0], "doprava_wedo" ) !== false ) {
      wc_add_notice( 'Pokud chcete doručit zboží prostřednictvím České pošty, zvolte prosím pobočku.', 'error' );
    }
  }
}

function doprava_gls_objednavka_zobrazit_pobocku( $order ) {
  if ( $order->has_shipping_method( 'doprava_gls' ) ) {
    foreach ( $order->get_shipping_methods() as $shipping_item_id => $shipping_item ) {
      if ( version_compare( WC_VERSION, '3.0', '<' ) ) {
        $pobocka = $order->get_item_meta( $shipping_item_id, 'GLS', true );
      } else {
        $pobocka = wc_get_order_item_meta( $shipping_item_id, 'GLS', true );
        
      }
    }
  }
}

add_filter( 'woocommerce_get_order_item_totals', 'add_custom_order_totals_row1', 30, 3 );
function add_custom_order_totals_row1( $total_rows, $order, $tax_display ) {
    if ( $order->has_shipping_method( 'doprava_gls' ) ) {
    foreach ( $order->get_shipping_methods() as $shipping_item_id => $shipping_item ) {
      if ( version_compare( WC_VERSION, '3.0', '<' ) ) {
        $pobocka = $order->get_item_meta( $shipping_item_id, 'GLS', true );
      } else {
        $pobocka = wc_get_order_item_meta( $shipping_item_id, 'GLS', true );
        
      }
    }
     $gran_total = $total_rows['order_total'];
    unset( $total_rows['order_total'] );
    $shipping = $total_rows['shipping'];
    unset($total_rows['shipping']);
    $total_rows['shipping'] = $shipping;
    $total_rows['recurr_not'] = array(
        'label' => __( 'Vybraná pobočka:', 'woocommerce' ),
        'value' => $pobocka,
    );
    $total_rows['order_total'] = $gran_total;
    return $total_rows;
    }
    else if ( $order->has_shipping_method( 'doprava_posta' ) ) {
    foreach ( $order->get_shipping_methods() as $shipping_item_id => $shipping_item ) {
      if ( version_compare( WC_VERSION, '3.0', '<' ) ) {
        $pobocka = $order->get_item_meta( $shipping_item_id, 'Česká pošta', true );
      } else {
        $pobocka = wc_get_order_item_meta( $shipping_item_id, 'Česká pošta', true );
        
      }
    }
     $gran_total = $total_rows['order_total'];
    unset( $total_rows['order_total'] );
    $shipping = $total_rows['shipping'];
    unset($total_rows['shipping']);
    $total_rows['shipping'] = $shipping;
    $total_rows['recurr_not'] = array(
        'label' => __( 'Vybraná pobočka:', 'woocommerce' ),
        'value' => $pobocka,
    );
    $total_rows['order_total'] = $gran_total;
    return $total_rows;
    }
    else if ( $order->has_shipping_method( 'doprava_wedo' ) ) {
    foreach ( $order->get_shipping_methods() as $shipping_item_id => $shipping_item ) {
      if ( version_compare( WC_VERSION, '3.0', '<' ) ) {
        $pobocka = $order->get_item_meta( $shipping_item_id, 'WE|DO', true );
      } else {
        $pobocka = wc_get_order_item_meta( $shipping_item_id, 'WE|DO', true );
        
      }
    }
     $gran_total = $total_rows['order_total'];
    unset( $total_rows['order_total'] );
    $shipping = $total_rows['shipping'];
    unset($total_rows['shipping']);
    $total_rows['shipping'] = $shipping;
    $total_rows['recurr_not'] = array(
        'label' => __( 'Vybraná pobočka:', 'woocommerce' ),
        'value' => $pobocka,
    );
    $total_rows['order_total'] = $gran_total;
    return $total_rows;
    }
    else if ( $order->has_shipping_method( 'doprava_zasilkovna' ) ) {
    foreach ( $order->get_shipping_methods() as $shipping_item_id => $shipping_item ) {
      if ( version_compare( WC_VERSION, '3.0', '<' ) ) {
        $pobocka = $order->get_item_meta( $shipping_item_id, 'DPD Pickup', true );
      } else {
        $pobocka = wc_get_order_item_meta( $shipping_item_id, 'DPD Pickup', true );
        
      }
    }
    $gran_total = $total_rows['order_total'];
    unset( $total_rows['order_total'] );
    $shipping = $total_rows['shipping'];
    unset($total_rows['shipping']);
    $total_rows['shipping'] = $shipping;
    $total_rows['recurr_not'] = array(
        'label' => __( 'Vybraná pobočka:', 'woocommerce' ),
        'value' => $pobocka,
    );
    $total_rows['order_total'] = $gran_total;
    return $total_rows;
    }
    else{
        $gran_total = $total_rows['order_total'];
    unset( $total_rows['order_total'] );
    $shipping = $total_rows['shipping'];
    unset($total_rows['shipping']);
    $total_rows['shipping'] = $shipping;
    $total_rows['order_total'] = $gran_total;
    return $total_rows;
    }
}
function doprava_gls_scripts_checkout() {
  if ( is_checkout() ) {
    $gls_settings = get_option( 'woocommerce_doprava_gls_settings' ); 
     $api_klic = $gls_settings['op1'];
     $api_klic2 = $gls_settings['op2']; ?>
      <script src="/wp-content/plugins/wcdoprava/js/gls.js"></script>
      <script type="text/javascript">
        var packetaApiKey = '<?php echo $api_klic; ?>';
        var packetaApiKey2= '<?php echo $api_klic2; ?>';
        var $storage_support = true;
        try {
          $storage_support = ( 'sessionStorage' in window && window.sessionStorage !== null );
          window.localStorage.setItem( 'doprava', 'test' );
          window.localStorage.removeItem( 'doprava' );
        } catch( err ) {
          $storage_support = false;
        }
        if ( $storage_support ) {
          jQuery( document ).ready(function( $ ) {
            $( document.body ).on( 'updated_checkout', function() {
              var doprava_gls = localStorage.getItem( 'doprava_gls' );
              if ( document.getElementById( 'packeta-point-info' ) !== null ) {
                var paragraph = document.getElementById( 'packeta-point-info' ).firstChild;
                if ( doprava_gls !== null ) {
                  paragraph.nodeValue = doprava_gls;
                  document.getElementById( 'packeta-point-id' ).value = doprava_gls;
                } else if ( paragraph !== "Zatím nevybráno" ) {
                  paragraph.nodeValue = "Zatím nevybráno";
                }
              }
            })
          });
        }
        function showSelectedPickupPoint(point) {
          var spanElement = document.getElementById( 'packeta-point-info' );
          var idElement = document.getElementById( 'packeta-point-id' );
          if ( point ) {
            spanElement.innerText = point.name;
            idElement.value = point.name;
            if ( $storage_support ) {
              localStorage.setItem( 'doprava_gls', point.name );
            }
          }
          else {
            if ( $storage_support ) {
              var doprava_gls = localStorage.getItem( 'doprava_gls' );
            } else {
              var doprava_gls = null;
            }
            if ( doprava_gls !== null ) {
              spanElement.innerText = doprava_gls;
              idElement.value = doprava_gls;
            } else {
              spanElement.innerText = "Zatím nevybráno";
              idElement.value = "";
            }
          }
        };
      </script>
    <?php 
  }
}

