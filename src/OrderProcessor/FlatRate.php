<?php

namespace Drupal\commerce_crude_shipping\OrderProcessor;

use Drupal\commerce_order\Adjustment;
use Drupal\commerce_order\Entity\OrderInterface;
use Drupal\commerce_order\OrderProcessorInterface;
use Drupal\commerce_price\Price;

class FlatRate implements OrderProcessorInterface {

  /**
   * {@inheritdoc}
   */
  public function process(OrderInterface $order) {
    foreach ($order->getItems() as $item) {
      /** @var \Drupal\commerce_order\Entity\OrderItem $item */
      $entity = $item->getPurchasedEntity();
      if ($entity->field_flat_rate_shipping_amount) {
        /** @var Drupal\commerce_price\Plugin\Field\FieldType\PriceItem $charge */
        $charge = $entity->field_flat_rate_shipping_amount->first();
        if (!empty($charge)) {
          $item->addAdjustment(new Adjustment([
            'type' => 'shipping',
            'label' => 'Shipping',
            'amount' => $charge->toPrice(),
            'source_id' => $order->id(),
          ]));
        }
      }
    }
  }

}
