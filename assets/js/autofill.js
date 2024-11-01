var HKGOV_REGION_MAP = {
  NT: "NEW TERRITORIES",
  KLN: "KOWLOON",
  HK: "HONG KONG",
};

var GOOGLE_REGION_MAP = {
  "NEW TERRITORIES": "NEW TERRITORIES",
  KOWLOON: "KOWLOON",
  "HONG KONG ISLAND": "HONG KONG",
};

function joinA(arr, sep) {
  return arr
    .filter(function (x) {
      return x && x.trim();
    })
    .join(sep);
}

function govAddressToDetails(data) {
  var id = data.Address.PremisesAddress.GeoAddress;
  var address = data.Address.PremisesAddress.EngPremisesAddress;

  var region = address.Region;

  var buildingInfo = address.EngBlock || {};
  var buildingInfo2 = joinA(
    [buildingInfo.BlockDescriptor, buildingInfo.BlockNo],
    " "
  );

  var streetInfo = address.EngStreet || {};
  var streetInfo2 = joinA(
    [streetInfo.BuildingNoFrom, streetInfo.StreetName],
    " "
  );

  var villeageInfo = address.EngVillage || {};
  var villeageInfo2 = joinA(
    [
      villeageInfo.VillageBuildingNoFrom,
      villeageInfo.LocationName,
      villeageInfo.VillageName,
    ],
    " "
  );

  var estateInfo = address.EngEstate || {};
  var estateInfo2 = estateInfo.EstateName;

  var building = buildingInfo2;
  var street = joinA(
    [streetInfo2, villeageInfo2, streetInfo.LocationName],
    ", "
  );
  var estate = joinA([estateInfo2, address.BuildingName], ", ");
  var district = address.EngDistrict.DcDistrict;

  return {
    id: id,
    building: building || "",
    estate: estate || "",
    street: street || "",
    district: district || "",
    region: HKGOV_REGION_MAP[region] || "",
  };
}

function addressToLine(details) {
  return joinA(
    [
      details.building,
      details.estate,
      details.street,
      details.district,
      details.region,
    ],
    ", "
  );
}

function initGovAutoFill({ el, onChange }) {
  jQuery(el).select2({
    ajax: {
      delay: 250,
      quietMillis: 250,
      url: "https://www.als.ogcio.gov.hk/lookup",
      data: function (params) {
        return { q: params, n: 10 };
      },
      processResults: function (data) {
        var addresses = data.SuggestedAddress || [];
        return {
          results: addresses.map((address) => {
            var details = govAddressToDetails(address);
            var text = addressToLine(details);
            return {
              id: details.id,
              text: text,
              address: details,
            };
          }),
        };
      },
    },
    minimumInputLength: 2,
  });

  jQuery(el).on("change", function () {
    const selected = jQuery(this).select2("data");
    onChange(selected);
  });
}

function initGoogleAutoFill({ el, onChange }) {
  var input = jQuery(el).get(0);

  if (!input) return;

  function findField(components, field) {
    for (var i = 0; i < components.length; i++) {
      var c = components[i];
      var types = c.types;
      for (var j = 0; j < types.length; j++) {
        if (types[j] === field) {
          return c.long_name || c.short_name;
        }
      }
    }
  }

  var options = {
    fields: ["address_components", "formatted_address"],
    componentRestrictions: { country: "hk" },
    // types: ["address"],
  };
  var autocomplete = new google.maps.places.Autocomplete(input, options);
  autocomplete.addListener("place_changed", function () {
    var place = autocomplete.getPlace();

    var building = findField(place.address_components, "premise");
    var streetNo = findField(place.address_components, "street_number");
    var streetName = findField(place.address_components, "route");
    var district = findField(place.address_components, "neighborhood");
    var region = findField(
      place.address_components,
      "administrative_area_level_1"
    );

    console.log(place);

    onChange({
      text: place.formatted_address,
      address: {
        building: building || "",
        estate: "",
        street: joinA([streetNo, streetName], ", ") || "",
        district: district || "",
        region: GOOGLE_REGION_MAP[region.toUpperCase()] || "",
      },
    });
  });
}

jQuery(document).ready(function () {
  var initAutoFill =
    hkaf.autofill_type === "hkgov"
      ? initGovAutoFill
      : hkaf.autofill_type === "google"
      ? initGoogleAutoFill
      : function () {};

  if (hkaf.autofill_for_billing === "1") {
    jQuery("#billing_search_address_field").insertBefore(
      "#billing_address_1_field"
    );

    jQuery("#billing_search_address_field .optional").hide();

    initAutoFill({
      el: "#billing_search_address",
      onChange: function (data) {
        var address = data.address;
        jQuery("#billing_state").val(address.region).change();
        jQuery("#billing_city").val(address.district);
        jQuery("#billing_address_1").val(address.building);
        jQuery("#billing_address_2").val(
          joinA([address.estate, address.street], ", ")
        );
      },
    });
  }

  if (hkaf.autofill_for_shipping === "1") {
    jQuery("#shipping_search_address_field").insertBefore(
      "#shipping_address_1_field"
    );

    jQuery("#shipping_search_address_field .optional").hide();

    initAutoFill({
      el: "#shipping_search_address",
      onChange: function (data) {
        var address = data.address;
        jQuery("#shipping_state").val(address.region).change();
        jQuery("#shipping_city").val(address.district);
        jQuery("#shipping_address_1").val(address.building);
        jQuery("#shipping_address_2").val(
          joinA([address.estate, address.street], ", ")
        );
      },
    });
  }
});
