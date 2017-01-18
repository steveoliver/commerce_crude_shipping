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
    $adjustment = new Price('0', $order->getTotalPrice()->getCurrencyCode());
    foreach ($order->getItems() as $item) {
      /** @var \Drupal\commerce_order\Entity\OrderItem $item */
      $entity = $item->getPurchasedEntity();
      if ($entity->field_flat_rate_shipping_amount) {
        $charge = $entity->field_flat_rate_shipping_amount->value;
        if (!empty($charge[0])) {
          $rate = new Price($charge[0]['number'], $charge[0]['currency_code']);
          $adjustment = $adjustment->add($rate->multiply($item->getQuantity()));
        }
      }
    }
    if (!$adjustment->isZero()) {
      $order->addAdjustment(new Adjustment([
        'type' => 'shipping',
        'label' => 'Shipping',
        'amount' => $adjustment,
      ]));
    }
  }

}
