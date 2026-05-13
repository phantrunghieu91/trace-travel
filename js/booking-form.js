document.addEventListener("DOMContentLoaded", (domEvent) => {
  const SERVICE_KEY = "services_cat_";
  const TRIP_KEY = "trip_";

  // Fetch data from api function
  const fetchProductData = async (productId, key) => {
    // render loading
    renderLoading(document.querySelector('.booking-form__add-ons'));
    const res = await fetch(api_data.ajax_url + `?action=get_trip_data&product_id=${productId}`);
    
    renderLoading();

    if (!res.ok) throw new Error("Failed to fetch product data");
    return res.json();
  };

  // Fetch services
  const fetchServices = async (catId) => {
    renderLoading(document.querySelector('.booking-form__add-ons'));

    const res = await fetch(api_data.ajax_url + `?action=get_services&nonce=${api_data.get_services_nonce}&cat_id=${catId}`);

    renderLoading();

    const resData = await res.json();
    if (!res.ok || !resData.success) {
      throw new Error(resData.data);
    }
    return resData.data;
  };

  // Save data to local storage for 1 hour
  const saveToLocalStorage = (key, data) => {
    const now = new Date();
    const item = {
      data: data,
      expiry: now.getTime() + 3600000,
    };
    localStorage.setItem(key, JSON.stringify(item));
  };

  const getFromLocalStorage = (key) => {
    const itemStr = localStorage.getItem(key);
    if (!itemStr) {
      return null;
    }
    const item = JSON.parse(itemStr);
    const now = new Date();
    if (now.getTime() > item.expiry) {
      localStorage.removeItem(key);
      return null;
    }
    return item.data;
  };

  const renderServices = services => {
    services.forEach((service) => {
      const option = document.createElement("option");
      option.value = service.id;
      option.textContent = service.name;
      servicesSelect.appendChild(option);
    });
  };

  const renderLoading = container => {
    if(!container) {
      // remove loading if exist
      document.querySelector("div.loading")?.remove();
      return;
    }
    const loadingEle = document.createElement("div");
    loadingEle.className = "loading";
    const loadingSpinner = document.createElement("span");
    loadingSpinner.classList.add(["dashicons", "dashicons-admin-generic"]);
    loadingEle.appendChild(loadingSpinner);
    container.appendChild(loadingEle);
  };

  // Form and Form's fields
  const bookingForm = document.querySelector("#booking-form");
  const bookingDate = document.querySelector("#booking-date");
  const bookingTime = document.querySelector("#booking-time");
  const bookingCarTypeSelect = document.querySelector("#booking-car-type");
  const addOnsContainer = document.querySelector(".booking-form__add-ons");
  const priceTotalEle = document.querySelector(".booking-form__total-price .amount");
  const addOnTotalEle = document.querySelector(".booking-form__add-on-price .amount");
  const carTypeTotalEle = document.querySelector(".booking-form__car-price .amount");
  const servicesSelect = document.querySelector("#services");
  const pickUpSelect = document.querySelector("#pick-up");

  // Handle when user changed pick up select box
  if (pickUpSelect) {
    pickUpSelect.addEventListener("change", async function () {
      servicesSelect.innerHTML = "";
      const defaultOpt = document.createElement("option");
      defaultOpt.value = "default";
      defaultOpt.textContent = "Select trip";
      servicesSelect.appendChild(defaultOpt);

      if (this.value !== "default") {
        document.querySelector("#pick-up + .booking-form__error-message") !== null && document.querySelector("#pick-up + .booking-form__error-message").remove();

        let services = getFromLocalStorage(SERVICE_KEY + this.value);

        if (!services) {
          services = await fetchServices(this.value);
          saveToLocalStorage(SERVICE_KEY + this.value, services);
        }

        renderServices(services);
      }
    });
  }

  // Handle when user changed service select box
  if (servicesSelect) {
    servicesSelect.addEventListener("change", async function () {
      // if select on have 1 option do nothing
      if (this.options.length === 1) return;

      if (this.value !== "default") {
        document.querySelector("#services + .booking-form__error-message") !== null && document.querySelector("#services + .booking-form__error-message").remove();

        let productData = getFromLocalStorage(TRIP_KEY + this.value);

        if (!productData) {
          productData = await fetchProductData(this.value);
          // Save data to local storage
          saveToLocalStorage(TRIP_KEY + this.value, productData);
        }

        // Get add on value
        addOnsContainer.innerHTML = "";
        if (productData.add_ons.length === 0) addOnsContainer.innerHTML = "<p>No add-ons available</p>";
        else {
          // check if addon is multi price or not
          productData.add_ons[0]["multi_price"] ? (addOnsContainer.dataset.multiPrices = true) : (addOnsContainer.dataset.multiPrices = false);
          // Render add ons
          productData.add_ons.forEach((addOn) => {
            const addOnLbl = document.createElement("label");
            const addOnName = document.createElement("span");
            const addOnPrice = document.createElement("span");
            const pricesContainer = document.createElement("div");
            const addOnChkBox = document.createElement("input");
            addOnChkBox.type = "checkbox";
            addOnChkBox.name = `add-on`;
            if (addOn["multi_price"]) {
              pricesContainer.className = "add-on__prices";
              const sedanPrice = document.createElement("span");
              sedanPrice.className = "sedan-price";
              sedanPrice.textContent = `$${addOn.prices.sedan}`;
              const suvPrice = document.createElement("span");
              suvPrice.className = "suv-price hide";
              suvPrice.textContent = `$${addOn.prices.suv}`;
              const vanPrice = document.createElement("span");
              vanPrice.className = "van-price hide";
              vanPrice.textContent = `$${addOn.prices.van}`;
              pricesContainer.append(sedanPrice, suvPrice, vanPrice);
              addOnChkBox.dataset.price = addOn.prices.sedan;
              addOnChkBox.dataset.sedanPrice = addOn.prices.sedan;
              addOnChkBox.dataset.suvPrice = addOn.prices.suv;
              addOnChkBox.dataset.vanPrice = addOn.prices.van;
              addOnName.textContent = addOn.name;
            } else {
              addOnChkBox.dataset.price = addOn.price;
              addOnName.textContent = addOn.name;
              addOnPrice.textContent = `$${addOn.price}`;
            }
            addOnChkBox.value = addOn.name;
            addOnChkBox.addEventListener("change", function () {
              if (this.checked) {
                priceTotalEle.dataset.addOnAmount = +priceTotalEle.dataset.addOnAmount + +this.dataset.price;
              } else {
                priceTotalEle.dataset.addOnAmount = +priceTotalEle.dataset.addOnAmount - +this.dataset.price;
              }
              addOnChkBox.dataset.selected = this.checked;
              addOnTotalEle.textContent = priceTotalEle.dataset.addOnAmount;
              updatePriceTotal();
            });
            if (addOn["multi_price"]) addOnLbl.append(addOnChkBox, addOnName, pricesContainer);
            else addOnLbl.append(addOnChkBox, addOnName, addOnPrice);
            addOnsContainer.appendChild(addOnLbl);
          });
        }

        // Render type of car
        bookingCarTypeSelect.innerHTML = '<option value="default">Select type of car</option>';
        const variations = productData.variations;
        variations.sort((a, b) => a.name.localeCompare(b.name));
        variations.forEach((variation) => {
          const option = document.createElement("option");
          option.textContent = variation.name;
          option.dataset.prdId = variation.id;
          option.value = variation.slug;
          option.dataset.price = variation.price;
          bookingCarTypeSelect.appendChild(option);
        });
      } else {
        document.querySelector("#booking-car-type").innerHTML = '<option value="default">Select type of car</option>';
        document.querySelector(".booking-form__add-ons").innerHTML = "";
      }
    });
  }

  // Update priceTotal
  const updatePriceTotal = () => {
    priceTotalEle.textContent = +priceTotalEle.dataset.carAmount + +priceTotalEle.dataset.addOnAmount;
  };
  // Change addon price with car type
  const updateAddOnsPrice = (carType) => {
    document.querySelectorAll('input[name="add-on"]').forEach((addOn) => {
      if (addOn.dataset.selected === "true") {
        priceTotalEle.dataset.addOnAmount = +priceTotalEle.dataset.addOnAmount - +addOn.dataset.price + +addOn.dataset[`${carType}Price`];
        addOnTotalEle.textContent = priceTotalEle.dataset.addOnAmount;
      }
      addOn.dataset.price = addOn.dataset[`${carType}Price`];
      updatePriceTotal();
    });
  };
  // Display car type price
  bookingCarTypeSelect.addEventListener("change", function () {
    if (this.value === "default") {
      carTypeTotalEle.textContent = "0";
      priceTotalEle.dataset.carAmount = 0;
      updatePriceTotal();
      return;
    }
    carTypeTotalEle.textContent = this.options[this.selectedIndex].dataset.price;
    if (addOnsContainer.dataset.multiPrices) {
      let priceClassName = "";
      switch (this.value) {
        case "07-seats-max-5-people":
          priceClassName = ".suv-price";
          updateAddOnsPrice("suv");
          break;
        case "16-seats-max-12-people":
          priceClassName = ".van-price";
          updateAddOnsPrice("van");
          break;
        default:
          priceClassName = ".sedan-price";
          updateAddOnsPrice("sedan");
      }
      document.querySelectorAll(`.add-on__prices > span:not(${priceClassName})`).forEach((ele) => {
        if (!ele.classList.contains("hide")) ele.classList.add("hide");
      });
      document.querySelectorAll(`.add-on__prices > span${priceClassName}`).forEach((ele) => {
        if (ele.classList.contains("hide")) ele.classList.remove("hide");
      });
    }
    priceTotalEle.dataset.carAmount = carTypeTotalEle.textContent;
    updatePriceTotal();
  });
  // Addons price
  document.querySelectorAll('input[name="add-on"]')?.forEach((addOn) => {
    addOn.addEventListener("change", function () {
      if (this.checked) {
        priceTotalEle.dataset.addOnAmount = +priceTotalEle.dataset.addOnAmount + +this.dataset.price;
      } else {
        priceTotalEle.dataset.addOnAmount = +priceTotalEle.dataset.addOnAmount - +this.dataset.price;
      }
      addOn.dataset.selected = this.checked;
      addOnTotalEle.textContent = priceTotalEle.dataset.addOnAmount;
      updatePriceTotal();
    });
  });

  // Handle display validate message
  const errorMessage = (eleToInsertAfter, message) => {
    if (!eleToInsertAfter.nextElementSibling) {
      const errorEle = document.createElement("div");
      errorEle.className = "booking-form__error-message";
      errorEle.textContent = message;
      eleToInsertAfter.insertAdjacentElement("afterend", errorEle);
    }
  };

  // Remove error message if had when data is valid
  [bookingDate, bookingTime, bookingCarTypeSelect].forEach((ele) => {
    ele.addEventListener("change", function () {
      if (ele.nextElementSibling) ele.nextElementSibling.remove();
    });
  });

  // Handle reset form
  const resetForm = (form) => {
    priceTotalEle.dataset.addOnAmount = 0;
    priceTotalEle.dataset.carAmount = 0;
    updatePriceTotal();
    document.querySelectorAll(".booking-form__error-message").forEach((ele) => ele.remove());
    bookingCarTypeSelect.innerHTML = '<option value="default">Select type of car</option>';
    addOnsContainer.innerHTML = "";
    addOnTotalEle.textContent = "0";
    carTypeTotalEle.textContent = "0";
    form.reset();
  };

  // Handle form submit
  bookingForm.addEventListener("submit", function (event) {
    // Form action
    const formAction = this.action;
    // Once use number for sercurity
    const nonce = document.querySelector("#my_form_nonce").value;
    // Get add on values
    let addOnValues = [];
    document.querySelectorAll('input[name="add-on"]').forEach((ele) => {
      if (ele.checked) addOnValues = [...addOnValues, { name: ele.value, price: ele.dataset.price, nameId: ele.dataset.nameId, priceId: ele.dataset.priceId }];
    });
    // Prevent form to run action
    event.preventDefault();
    // Handle validate
    const isPickupValid = pickUpSelect ? pickUpSelect.value !== "default" : true;
    const isServicesValid = servicesSelect ? servicesSelect.value !== "default" : true;
    const isDateValid = bookingDate.value !== "";
    const isTimeValid = bookingTime.value !== "";
    const isCarTypeValid = bookingCarTypeSelect.value !== "default";
    const isFormValid = isPickupValid && isServicesValid && isCarTypeValid && isDateValid && isTimeValid;

    if (!isPickupValid) errorMessage(pickUpSelect, "Please choose a pick up location first!");
    if (!isServicesValid) errorMessage(servicesSelect, "Please choose a trip first!");
    if (!isDateValid) errorMessage(bookingDate, "Please choose date!");
    if (!isTimeValid) errorMessage(bookingTime, "Please choose time!");
    if (!isCarTypeValid) errorMessage(bookingCarTypeSelect, "Please choose a car type first!");

    if (isFormValid) {
      // Send POST request to add item to cart then navigate to checkout
      jQuery.ajax({
        url: api_data.ajax_url,
        type: "POST",
        data: {
          action: "handle_booking",
          nonce: nonce,
          bookingData: {
            product_id: servicesSelect ? servicesSelect.value : document.querySelector(`input[name='product-id'`).value,
            variation_id: bookingCarTypeSelect.options[bookingCarTypeSelect.selectedIndex].dataset.prdId,
            add_on_values: addOnValues,
            total_price: priceTotalEle.textContent,
            departure_date: bookingDate.value,
            departure_time: bookingTime.value,
            car_type: { [bookingCarTypeSelect.name]: bookingCarTypeSelect.value },
          },
        },
        success: function (res) {
          console.log(res);
          setTimeout(() => {
            window.location.href = formAction;
          }, 800);
        },
        error: function (error) {
          console.log(error);
        },
      });
    }
  });

  // Handle form reset
  bookingForm.addEventListener("reset", function (evt) {
    // evt.preventDefault();
    resetForm(this);
  });
});
