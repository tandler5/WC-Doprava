<?php
// https://github.com/woocommerce/woocommerce/wiki/Shipping-Method-API
class WC_Shipping_doprava_Zasilkovna extends WC_Shipping_Method {
  public function __construct( $instance_id = 0 ) {
    $this->instance_id = absint( $instance_id );
    $this->id = 'doprava_zasilkovna';
    $this->method_title = 'DPD Pickup';
    $this->method_description = 'Umožňí doručení do jednoho z výdejních míst, které si příjemce zásilky vybere podle svého přání.';
    $aktivace_zasilkovna = get_option( 'wc_doprava_doprava_zasilkovna' );
    $this->enabled = $aktivace_zasilkovna;
    $this->supports = array(
      'shipping-zones',
      'settings',
      'instance-settings',
      'instance-settings-modal',
    );
    $this->init();
  }

  function init() {
    $this->init_form_fields();
    $this->init_settings();
    $this->init_instance_form_fields();
    $this->init_instance_settings();
    $this->title = $this->get_option( 'zasilkovna_nazev' );
    add_action( 'woocommerce_update_options_shipping_' . $this->id, array( $this, 'process_admin_options' ) );
  }
 
  public function calculate_shipping( $package = array() ) {
    $rate = array(
      'id' => $this->get_rate_id(),
      'label' => $this->title,
      'cost' => $this->get_option( 'zasilkovna_zakladni-cena' ),
      'package' => $package,
    );
    $this->add_rate( $rate );
  }

  public function init_form_fields() {
    $this->form_fields = array (
      'op1' => array(
        'title'       => 'DPD Boxy',
        'label'       => 'Zobrazí se ve widgetu i výdejní místa s DPD boxy',
        'type'        => 'checkbox',
        'default'     => 'yes',
      )
  );
  } 

  public function init_instance_form_fields() {
    $this->instance_form_fields = array(
      'zasilkovna_nazev' => array(
        'title' => 'Název',
        'type' => 'text',
        'description' => 'Název pro zobrazení v eshopu.',
        'default' => 'DPD Pickup',
        'css' => 'width: 300px;'
      ),
      'zasilkovna_zakladni-cena' => array(
        'title' => 'Základní cena',
        'type' => 'price',
        'description' => 'Pokud nebude cena vyplněna, tak bude nulová.',
        'default' => '',
        'css' => 'width: 100px;',
        'placeholder' => wc_format_localized_price( 0 )
      ),
    );
  }      
}
