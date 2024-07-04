<?php
class FRZR_Contribution_Completed_Email extends WC_Email {
    public function __construct() {
        $this->id = 'frzr_contribution_completed_email';
        $this->customer_email = true;
        $this->title = 'Contribution completed';

        $this->subject = 'Thank You for Completing Your Contribution';
        $this->heading = 'Contribution Completed: Your Support Matters';

        // Set your custom template paths
        $this->template_html  = '/completed-contribution.php';
        $this->template_plain = '/completed-contribution.php';
        $this->template_base = FRZR_PATH . '/src/Hook/WooCommerce/Notification/templates';

        parent::__construct();

    }

    public function trigger( $order_id ) {
        $this->setup_locale();

        $order = wc_get_order( $order_id );

        // Assuming contribution details are stored in order meta, adjust accordingly
        $contribution_date = $order->get_date_created();
        $contribution_amount = $order->get_total();

        $this->object                         = $order;
        $this->recipient                      = $this->object->get_billing_email();
        $this->placeholders['{contribution_date}'] = wc_format_datetime( $contribution_date );
        $this->placeholders['{contribution_amount}'] = wc_price( $contribution_amount );

        if ( $this->is_enabled() && $this->get_recipient() ) {
            $this->send( $this->get_recipient(), $this->get_subject(), $this->get_content(), $this->get_headers(), $this->get_attachments() );
        }

        $this->restore_locale();
    }

    public function get_content_html() {
        return $this->get_email_content( false );
    }

    public function get_content_plain() {
        return $this->get_email_content( true );
    }

    private function get_email_content( $plain_text ) {
        return wc_get_template_html( $plain_text ? $this->template_plain : $this->template_html, array(
            'order'              => $this->object,
            'email_heading'      => $this->get_heading(),
            'sent_to_admin'      => false,
            'plain_text'         => $plain_text,
            'email'              => $this,
        ), '', $this->template_base );
    }
}
