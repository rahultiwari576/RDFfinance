$(function () {
    const statesAndCities = {
        "Andhra Pradesh": ["Visakhapatnam", "Vijayawada", "Guntur", "Nellore", "Kurnool", "Tirupati", "Rajahmundry", "Kakinada"],
        "Arunachal Pradesh": ["Itanagar", "Naharlagun", "Pasighat"],
        "Assam": ["Guwahati", "Silchar", "Dibrugarh", "Jorhat", "Nagaon", "Tinsukia", "Tezpur"],
        "Bihar": ["Patna", "Gaya", "Bhagalpur", "Muzaffarpur", "Purnia", "Darbhanga", "Arrah", "Begusarai"],
        "Chhattisgarh": ["Raipur", "Bhilai", "Bilaspur", "Korba", "Rajnandgaon", "Raigarh", "Jagdalpur"],
        "Goa": ["Panaji", "Margao", "Vasco da Gama", "Mapusa"],
        "Gujarat": ["Ahmedabad", "Surat", "Vadodara", "Rajkot", "Bhavnagar", "Jamnagar", "Junagadh", "Gandhinagar"],
        "Haryana": ["Faridabad", "Gurgaon", "Panipat", "Ambala", "Yamunanagar", "Rohtak", "Hisar", "Karnal"],
        "Himachal Pradesh": ["Shimla", "Dharamshala", "Solan", "Mandi"],
        "Jharkhand": ["Jamshedpur", "Dhanbad", "Ranchi", "Bokaro", "Deoghar", "Phusro", "Hazaribagh"],
        "Karnataka": ["Bangalore", "Hubli", "Mysore", "Gulbarga", "Belgaum", "Mangalore", "Davanagere", "Bellary"],
        "Kerala": ["Thiruvananthapuram", "Kochi", "Kozhikode", "Kollam", "Thrissur", "Alappuzha", "Palakkad"],
        "Madhya Pradesh": ["Indore", "Bhopal", "Jabalpur", "Gwalior", "Ujjain", "Sagar", "Dewas", "Satna"],
        "Maharashtra": ["Mumbai", "Pune", "Nagpur", "Thane", "Pimpri-Chinchwad", "Nashik", "Kalyan-Dombivli", "Vasai-Virar", "Aurangabad", "Navi Mumbai", "Solapur", "Mira-Bhayandar"],
        "Manipur": ["Imphal"],
        "Meghalaya": ["Shillong", "Tura"],
        "Mizoram": ["Aizawl", "Lunglei"],
        "Nagaland": ["Dimapur", "Kohima"],
        "Odisha": ["Bhubaneswar", "Cuttack", "Rourkela", "Berhampur", "Sambalpur", "Puri", "Balasore"],
        "Punjab": ["Ludhiana", "Amritsar", "Jalandhar", "Patiala", "Bathinda", "Mohali", "Hoshiarpur"],
        "Rajasthan": ["Jaipur", "Jodhpur", "Kota", "Bikaner", "Ajmer", "Udaipur", "Bhilwara", "Alwar"],
        "Sikkim": ["Gangtok"],
        "Tamil Nadu": ["Chennai", "Coimbatore", "Madurai", "Tiruchirappalli", "Salem", "Tiruppur", "Erode", "Vellore"],
        "Telangana": ["Hyderabad", "Warangal", "Nizamabad", "Khammam", "Karimnagar", "Ramagundam", "Mahbubnagar"],
        "Tripura": ["Agartala"],
        "Uttar Pradesh": ["Lucknow", "Kanpur", "Ghaziabad", "Agra", "Meerut", "Varanasi", "Prayagraj", "Bareilly", "Aligarh", "Moradabad", "Noida", "Gorakhpur"],
        "Uttarakhand": ["Dehradun", "Haridwar", "Roorkee", "Haldwani", "Rudrapur"],
        "West Bengal": ["Kolkata", "Howrah", "Asansol", "Siliguri", "Durgapur", "Bardhaman", "Malda", "Baharampur"],
        "Andaman and Nicobar Islands": ["Port Blair"],
        "Chandigarh": ["Chandigarh"],
        "Dadra and Nagar Haveli and Daman and Diu": ["Daman", "Diu", "Silvassa"],
        "Delhi": ["Delhi", "New Delhi"],
        "Jammu and Kashmir": ["Srinagar", "Jammu", "Anantnag"],
        "Ladakh": ["Leh", "Kargil"],
        "Lakshadweep": ["Kavaratti"],
        "Puducherry": ["Puducherry", "Ozhukarai"]
    };

    window.initializeStates = (stateSelect) => {
        stateSelect.empty().append('<option value="">Select State...</option>');
        Object.keys(statesAndCities).sort().forEach(state => {
            stateSelect.append(`<option value="${state}">${state}</option>`);
        });
    };

    window.populateCities = (state, citySelect, selectedCity = null) => {
        citySelect.empty().append('<option value="">Select City...</option>');
        if (state && statesAndCities[state]) {
            statesAndCities[state].sort().forEach(city => {
                const selected = (city === selectedCity) ? 'selected' : '';
                citySelect.append(`<option value="${city}" ${selected}>${city}</option>`);
            });
            citySelect.prop('disabled', false);
        } else {
            citySelect.prop('disabled', true);
        }
    };

    // Create User Address Logic
    $('#create_address_type').on('change', function () {
        if ($(this).val()) {
            $('#create_address_container').removeClass('d-none').hide().fadeIn();
            initializeStates($('#create_state'));
        } else {
            $('#create_address_container').fadeOut();
        }
    });

    $('#create_state').on('change', function () {
        populateCities($(this).val(), $('#create_city'));
    });

    $('#create_pincode').on('input', function () {
        this.value = this.value.replace(/\D/g, '').slice(0, 6);
    });

    // Edit User Address Logic
    $('#edit_address_type').on('change', function () {
        if ($(this).val()) {
            $('#edit_address_container').removeClass('d-none').hide().fadeIn();
            if ($('#edit_state option').length <= 1) {
                initializeStates($('#edit_state'));
            }
        } else {
            $('#edit_address_container').fadeOut();
        }
    });

    $('#edit_state').on('change', function () {
        populateCities($(this).val(), $('#edit_city'));
    });

    $('#edit_pincode').on('input', function () {
        this.value = this.value.replace(/\D/g, '').slice(0, 6);
    });
});
