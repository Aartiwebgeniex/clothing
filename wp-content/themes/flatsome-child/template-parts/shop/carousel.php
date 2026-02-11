<div id="homepage-slider" class="st-slider">

  <?php
  $shop_carousel_re = get_field('shop_carousel_re', 'option');
  $slide_counter    = 1;
  if ($shop_carousel_re):
    foreach ($shop_carousel_re as $slide):
      echo '<input type="radio" class="cs_anchor radio" name="slider" id="slide' . esc_attr($slide_counter) . '"/>';
      $slide_counter++;
    endforeach;
    ?>
    <input type="radio" class="cs_anchor radio" name="slider" id="play1" checked="" />

    <div class="images">
      <div class="images-inner">
        <?php
        foreach ($shop_carousel_re as $slide):
          $shop_slide    = $slide['shop_slide'];
          $shop_caro_url = $slide['shop_caro_url'];
            ?>
          <div class="image-slide">
            <div class="image">
              <a href="<?php echo esc_url($shop_caro_url); ?>">
                <img src="<?php echo esc_url($shop_slide); ?>" alt="">
              </a>
            </div>
          </div>
        <?php endforeach; ?>
      </div>
    </div>

    <div class="labels">
      <?php
      $slide_counter = 1;
      foreach ($shop_carousel_re as $slide):
        ?>
        <label for="slide<?php echo esc_attr($slide_counter); ?>" class="label">Label here</label>
        <?php
        $slide_counter++;
      endforeach;
      ?>

      <div class="fake-radio">
        <?php
        $slide_counter = 1;
        foreach ($shop_carousel_re as $slide):
          ?>
          <label for="slide<?php echo esc_attr($slide_counter); ?>" class="radio-btn"></label>
          <?php
          $slide_counter++;
        endforeach;
        ?>
      </div>
    </div>
  <?php else: ?>
    <!-- No slides found -->
  <?php endif; ?>
</div>

<style>
  #homepage-slider {
    margin-bottom: 50px;
    display: inline-block;
    width: 100%;
  }



  #homepage-slider .images {
    overflow: hidden;
    top: 0;
    bottom: 0;
    left: 0;
    right: 0;
    width: 100%;
  }

  #homepage-slider .images-inner {
    width: 500%;
    transition: all 800ms cubic-bezier(0.770, 0.000, 0.175, 1.000);
    transition-timing-function: cubic-bezier(0.770, 0.000, 0.175, 1.000);
  }

  #homepage-slider .image-slide {
    width: 20%;
    float: left;
  }

  #homepage-slider .image-slide,
  .fake-radio,
  .radio-btn {
    transition: all 0.5s ease-out;
  }

  #homepage-slider .cs_anchor.radio {
    display: none;
  }

  #homepage-slider .labels .label {
    display: none;
    opacity: 0;
    position: absolute;
  }


  #homepage-slider .fake-radio {
    width: 100%;
    float: right;
    text-align: center;
    display: inline-block;
  }

  #homepage-slider .image a {
    width: 100%;
    display: inline-block;
  }

  #homepage-slider .image a img {
    width: 100%;
    display: inline-block;
  }




  #homepage-slider .radio-btn {
    width: 9px;
    height: 9px;
    border-radius: 5px;
    background: gray;
    display: inline-block !important;
    margin: 0 1px;
    cursor: pointer;
  }

  /* Color of bullets - END */

  /* Text of slides - END */

  /* Calculate AUTOPLAY for BULLETS */
  @keyframes bullet {

    0%,
    33.32333333333334% {
      background: red;
    }

    33.333333333333336%,
    100% {
      background: gray;
    }
  }



  /* Calculate AUTOPLAY for BULLETS - END */

  /* Calculate AUTOPLAY for SLIDES */
  @keyframes slide {

    0%,
    25.203252032520325% {
      margin-left: 0;
    }

    33.333333333333336%,
    58.53658536585366% {
      margin-left: -100%;
    }
  }

  .st-slider>#play1:checked~.images .images-inner {
    animation: slide 12300ms infinite;
  }

  /* Calculate AUTOPLAY for SLIDES - END */

  /* Calculate AUTOPLAY for CAPTION */
  @keyframes caption {

    0%,
    33.32333333333334% {
      opacity: 1;
    }

    33.333333333333336%,
    100% {
      opacity: 0;
    }
  }


  /* Calculate AUTOPLAY for CAPTION - END */
</style>
<script>
  let currentSlide = 1;
  const numSlides = document.querySelectorAll('.image-slide').length;

  // Function to switch to the next slide
  function nextSlide() {
    currentSlide++;
    if (currentSlide > numSlides) {
      currentSlide = 1;
    }
    document.querySelector(`#slide${currentSlide}`).checked = true;
  }

  // Start autoplay
  setInterval(nextSlide, 4100);  // Switch every 4.1 seconds

  const style = document.createElement('style');
  let css = '';

  // Generate dynamic CSS for labels
  for (let i = 1;i <= numSlides;i++) {
    const animationDelay = (i - 1) * 2100; // Adjust this value as needed
    css += `#slide${i}:checked ~ .labels .label:nth-child(${i}) { opacity: 1; }\n`;
    css += `#slide${i}:checked ~ div .fake-radio .radio-btn:nth-child(${i}) { background: red; }\n`;
    css += `#slide${i}:checked ~ .images .images-inner { margin-left: ${(i - 1) * -100}%; }\n`;
    css += `#play1:checked ~ .labels .label:nth-child(${i}) { animation: caption 12300ms infinite ${animationDelay}ms; }\n`;
  }

  // Append the generated CSS to the head
  style.innerHTML = css;
  document.head.appendChild(style);

  // Set the "checked" attribute on the first slide radio input
  const firstSlideRadio = document.querySelector('#slide1');
  if (firstSlideRadio) {
    firstSlideRadio.setAttribute('checked', 'checked');
  }

  // Re-enable autoplay by setting "checked" on #play1 after a short delay
  setTimeout(() => {
    const autoPlayRadio = document.querySelector('#play1');
    if (autoPlayRadio) {
      autoPlayRadio.setAttribute('checked', 'checked');
    }
  }, 1000);  // 1 second delay


</script>