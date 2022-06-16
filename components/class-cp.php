<?php
// https://github.com/woocommerce/woocommerce/wiki/Shipping-Method-API
class WC_Shipping_doprava_Posta extends WC_Shipping_Method {
  public function __construct( $instance_id = 0 ) {
    $this->instance_id = absint( $instance_id );
    $this->id = 'doprava_posta';
    $this->method_title = 'Česká pošta';
    $this->method_description = 'Umožňete zákazníkům vybrat si preferovanou Balíkovnu (na poště i mimo poštu).';
    $aktivace_posta = get_option( 'wc_doprava_doprava_posta' );
    $this->enabled = $aktivace_posta;
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
    $this->title = $this->get_option( 'posta_nazev' );
    add_action( 'woocommerce_update_options_shipping_' . $this->id, array( $this, 'process_admin_options' ) );
  }
 
  public function calculate_shipping( $package = array() ) {
    $rate = array(
      'id' => $this->get_rate_id(),
      'label' => $this->title,
      'cost' => $this->get_option( 'posta_zakladni-cena' ),
      'package' => $package,
    );
    $this->add_rate( $rate );
  }

  public function init_form_fields() {
    $this->form_fields = array (
      'op1' => array(
        'title'       => 'Pošta',
        'label'       => 'Zobrazení se ve widgetu výdejní místa České pošty',
        'type'        => 'checkbox',
        'default'     => 'yes',
      ),
      'op2' => array(
        'title'       => 'Balíkovna',
        'label'       => 'Zobrazení se ve widgetu výdejní místa Balíkovny',
        'type'        => 'checkbox',
        'default'     => 'yes',
      )
  );
  } 

  public function init_instance_form_fields() {
    $this->instance_form_fields = array(
      'posta_nazev' => array(
        'title' => 'Název',
        'type' => 'text',
        'description' => 'Název pro zobrazení v eshopu.',
        'default' => 'Česká Pošta',
        'css' => 'width: 300px;'
      ),
      'posta_zakladni-cena' => array(
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