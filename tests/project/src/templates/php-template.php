<?php
if (!isset($publicArea)) {
    $publicArea = false;
}
/**
 * используй переменную $publicArea, чтобы проверить, публичный это прайсинг или тот который в админке
 */

$currency = HLanguage::isRussian() ? HAmount::CURRENCY_RUB : HAmount::CURRENCY_USD;
?>

<script>
  var pricingPlans = {
    free: '<?= Partner::PRICING_PLAN_FREE ?>',
    widgets: '<?= Partner::PRICING_PLAN_WIDGETS_ONLY ?>',
    loyalty: '<?= Partner::PRICING_PLAN_LOYALTY_ONLY ?>',
    all: '<?= Partner::PRICING_PLAN_ALL_INCLUDED ?>', 
  }
</script>

<div class="subscription js-subscription">
  <h1 class="page-title subscription__title">
    <?= __("Subscription") ?>
  </h1>
  <div class="subscription__period">
    <div class="subscription-period js-subscription-period">
      <ul class="subscription-period__radiogroup">
        <li class="subscription-period__radiogroup-item">
          <input class="subscription-period__radio" type="radio" name="subscription" id="subscription1" value="1" checked="">
          <label for="subscription1" class="subscription-period__option js-subscription-option is-active" data-discount="0" data-period="1">
            <?= __("Monthly") ?>
          </label>
        </li>
        <li class="subscription-period__radiogroup-item">
          <input class="subscription-period__radio" type="radio" name="subscription" id="subscription2" value="6">
          <label for="subscription2" class="subscription-period__option js-subscription-option has-discount" data-discount="10" data-period="6">
            <?= __("Half a year") ?>
          </label>
        </li>
        <li class="subscription-period__radiogroup-item">
          <input class="subscription-period__radio" type="radio" name="subscription" id="subscription3" value="12">
          <label for="subscription3" class="subscription-period__option js-subscription-option has-discount" data-discount="20" data-period="12">
            <?= __("Annual") ?>
          </label>
        </li>
      </ul>
    </div>

    <div class="bit-incut">
      <span class="bit-incut__text">
        <?= _("You can pay with BIT-tokens!") ?>
      </span>
      <svg class="bit-incut__icon">
        <use xlink:href="#bitrewards"></use>
      </svg>
    </div>
  </div>
  <p class="subscription__remark">
    <?= __("Specify the number of visits per month to your site:") ?> 
  </p>
  <div class="pricing-slider js-pricing-slider">
    <div class="pricing-slider__body">
      <ul class="pricing-slider__axis">
        <li class="pricing-slider__axis-item is-active js-pricing-slider-scale" data-base-price="<?=HLanguage::isRussian() ? 10000 : 199 ?>" data-bit-base-price="<?= HAmount::convertToBitCached(HLanguage::isRussian() ? 10000 : 199, $currency) ?>">
          <span class="pricing-slider__axis-title js-pricing-slider-segment-point is-active">
            < 10 000
          </span>
        </li>
        <li class="pricing-slider__axis-item js-pricing-slider-scale" data-base-price="<?=HLanguage::isRussian() ? 20000 : 399 ?>" data-bit-base-price="<?= HAmount::convertToBitCached(HLanguage::isRussian() ? 20000 : 399, $currency) ?>">
          <span class="pricing-slider__axis-title js-pricing-slider-segment-point">
            < 100 000
          </span>
        </li>
        <li class="pricing-slider__axis-item js-pricing-slider-scale" data-base-price="<?=HLanguage::isRussian() ? 40000 : 799 ?>" data-bit-base-price="<?= HAmount::convertToBitCached(HLanguage::isRussian() ? 40000 : 799, $currency) ?>">
          <!--  !-->
          <span class="pricing-slider__axis-title pricing-slider__axis-title_viewtype_short js-pricing-slider-segment-point">
            < 500 000
          </span>
        </li>
        <li class="pricing-slider__axis-item js-pricing-slider-scale" data-base-price="<?=HLanguage::isRussian() ? 60000 : 999 ?>">
          <!-- data-bit-base-price="HAmount::convertToBitCached(HLanguage::isRussian() ? 60000 : 999, $currency) ?>" !-->
          <span class="pricing-slider__axis-title pricing-slider__axis-title_viewtype_short js-pricing-slider-segment-point">
            < 1 000 000
          </span>
        </li>
        <li class="pricing-slider__axis-item pricing-slider__axis-item_type_last js-pricing-slider-scale" data-base-price="special">
          <span class="pricing-slider__axis-title pricing-slider__axis-title_viewtype_short pricing-slider__axis-title_content_last js-pricing-slider-segment-point">
            <?= __("Larger") ?>
          </span>
        </li>
      </ul>
      <div class="form-field-box form-field-box_content_pricing-slider">
        <div class="pricing-slider__fake-input js-pricing-slider-fake-input"></div>
        <span class="pricing-slider__thumb js-pricing-slider-thumb"></span>
        <input value="0" max="100" autocomplete="off" type="range" class="pricing-slider__input js-pricing-slider-input">
      </div>
    </div>
  </div>
</div>

<div class="pricing-options">
  <h2 class="pricing-options__title">
    <?= __("Available tools:") ?>
  </h2>
  <div class="pricing-options__body js-price-option">
    <ul class="pricing-options__list">
      <li class="pricing-options__item">
        <div class="price-option">
          <div class="price-option__desc">
            <ul class="price-option__list">
              <li class="price-option__item">
                <h5 class="price-option__item-title">
                  <?= __("Smart tools module:") ?>
                </h5>
                <div class="price-option__item-text price-option__item-text_viewtype_large pure-content">
                  <ul class="disc">
                    <li><?= __("Maxi-Cart") ?></li>
                    <li><?= __("Motivational tool") ?></li>
                    <li><?= __("Social share tool") ?></li>
                    <li><?= __("Trader tool") ?></li>
                    <li><?= __("Gambler tool") ?></li>
                    <li><?= __("Survey tool") ?></li>
                  </ul>
                </div>
              </li>
            </ul>
          </div>
        </div>
      </li>
      <li class="pricing-options__item">
        <div class="price-option">
          <div class="price-option__desc">
            <ul class="price-option__list">
              <li class="price-option__item">
                <h5 class="price-option__item-title">
                  <?= __("Gift Cards module:") ?>
                </h5>
                <div class="price-option__item-text price-option__item-text_viewtype_large pure-content">
                  <ul class="disc">
                    <li><?= __("Electronic gift cards") ?></li>
                    <li><?= __("Give a gift online") ?></li>
                    <li><?= __("Collective gift") ?></li>
                    <li><?= __("Goods as a gift") ?></li>
                  </ul>
                </div>
              </li>
              <li class="price-option__item">
                <h5 class="price-option__item-title">
                  <?= __("Offline solutions:") ?>
                </h5>
                <div class="price-option__item-text price-option__item-text_viewtype_large pure-content">
                  <ul class="disc">
                    <li><?= __('AirDrop') ?></li>
                    <li><?= __('Gift Cards') ?></li>
                  </ul>
                </div>
              </li>
            </ul>
          </div>
        </div>
      </li>
      <li class="pricing-options__item">
        <div class="price-option">
          <div class="price-option__desc">
            <ul class="price-option__list">
              <li class="price-option__item">
                <h5 class="price-option__item-title">
                  <?= __("Referral solutions:") ?>
                </h5>
                <div class="price-option__item-text price-option__item-text_viewtype_large pure-content">
                  <ul class="disc">
                    <li><?= __("Referral tool for store's visitors") ?></li>
                    <li><?= __("Referral tool for influencers and opinion leaders") ?></li>
                  </ul>
                </div>
              </li>
              <li class="price-option__item">
                <h5 class="price-option__item-title">
                  <b><?= __("Comprehensive loyalty program") ?></b>
                </h5>
              </li>
            </ul>
          </div>
        </div>
      </li>
    </ul>
  </div>

  <div class="total">
    <span class="total__old-price js-total-old">9000</span>
    <span class="total__amount">
      <span class="pricing-currency"><?= __("from") ?></span> <?= !HLanguage::isRussian() ? '<span class="pricing-currency pricing-currency_viewtype_usd">$</span>' : '' ?><span class="js-total"><?= HLanguage::isRussian() ? 10000: 199 ?></span><?= HLanguage::isRussian() ? '<span class="rouble pricing-currency"></span>': '' ?><span class="pricing-currency"><?= __("/mo.") ?></span>
      <span class="bit-price">
        = <?= __("from") ?> <span class="js-total-bit">10000</span> BIT
      </span>
    </span>
    <span class="total__special">
      <?= __("By agreement") ?>
    </span>
  </div>
  <div class="price-option__text">
    <?= __("The final cost depends on the scenario and the module set.") ?>
  </div>
  <div class="price-action price-action_content_all-inclusive">
    <?php if($publicArea) { ?>
      <button type="button" class="button button_viewtype_primary button_content_subscribe js-show-popup is-all-inclusive" data-popup=".js-get-price-popup">
        <span class="button__text">
          <?= __("Get the price") ?>
        </span>
      </button>
    <?php } else { ?>
      <a class="button button_viewtype_primary button_content_subscribe js-subscribe-signup" href="<?= $this->url('request/index', ['subject' => PartnerV2RequestSubject::PAID_PLAN]) ?>">
        <span class="button__text">
          <?= __("Sign up") ?>
        </span>
      </a>
    <?php } ?>
  </div>
  <?php if($publicArea) { ?>
    </div>
  <?php } ?>
</div>
