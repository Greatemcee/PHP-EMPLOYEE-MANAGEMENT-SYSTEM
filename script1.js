document.addEventListener("DOMContentLoaded", function () {
  // Time In/Out Toggle Functionality
  let isTimeIn = true;
  const timeButton = document.getElementById('timeButton');
  const timeDisplay = document.getElementById('timeDisplay');

  if (timeButton) {
    timeButton.addEventListener('click', function () {
      const currentTime = new Date().toLocaleTimeString();
      if (isTimeIn) {
        timeButton.innerText = 'Time Out';
        timeButton.style.backgroundColor = '#f3c23f';
        if (timeDisplay) timeDisplay.innerHTML = 'Time In at: ' + currentTime;
      } else {
        timeButton.innerText = 'Time In';
        timeButton.style.backgroundColor = '#366d4a';
        if (timeDisplay) timeDisplay.innerHTML += '<br>Time Out at: ' + currentTime;
      }
      isTimeIn = !isTimeIn;
    });
  }

  // Modal Functionality for Payout
  const modal = document.getElementById("payoutModal");
  const btn = document.getElementById("withdrawBtn");
  const span = document.getElementsByClassName("close")[0];

  if (btn && modal) {
    btn.onclick = function () {
      modal.style.display = "flex";
    };
  }

  if (span && modal) {
    span.onclick = function () {
      modal.style.display = "none";
    };
  }

  window.onclick = function (event) {
    if (event.target == modal) {
      modal.style.display = "none";
    }
  };

  // Capture Withdraw Amount when Form is Submitted
  const payoutForm = document.querySelector('form[action="process_payout.php"]');
  if (payoutForm) {
    payoutForm.addEventListener("submit", function (event) {
      event.preventDefault(); // Prevent default form submission

      // Get the amount entered by the user in the "Amount to Withdraw" field
      const withdrawAmount = parseFloat(document.getElementById('amount').value);

      // Check if the amount entered is valid
      if (isNaN(withdrawAmount) || withdrawAmount <= 0) {
        alert('Please enter a valid amount to withdraw.');
        return;
      }

      // Here you can process the withdraw amount further or submit the form
      console.log('Amount to withdraw:', withdrawAmount);

      // If the form is valid, you can submit it normally, for example using fetch:
      const formData = new FormData(payoutForm);
      formData.append('withdrawal_amount', withdrawAmount); // Append the withdrawal amount to the form data

      fetch(payoutForm.action, {
        method: 'POST',
        body: formData
      })
      .then(response => response.text())
      .then(data => {
        alert("Payout request submitted successfully!");
        modal.style.display = "none"; // Hide the modal after submission
      })
      .catch(error => {
        console.error('Error processing payout:', error);
        alert('There was an issue processing your request.');
      });
    });
  }

  // Settings Page Functionality
  const saveSettingsBtn = document.getElementById("saveSettings");
  const cancelSettingsBtn = document.getElementById("cancelSettings");

  if (saveSettingsBtn) {
    saveSettingsBtn.addEventListener("click", function (event) {
      event.preventDefault(); // Prevent the form from submitting normally
      const form = document.querySelector('form');
      const formData = new FormData(form);

      fetch('dashboard.php?page=settings', {
        method: 'POST',
        body: formData,
      })
      .then(response => response.text())
      .then(data => {
        // Update the profile picture if uploaded
        const profilePicInput = formData.get('profile-pic');
        if (profilePicInput && profilePicInput.name) {
          document.querySelector('.profile-pic img').src = 'uploads/' + profilePicInput.name;
        }
        alert("Profile updated successfully!");
      })
      .catch(error => console.error('Error:', error));
    });
  }

  if (cancelSettingsBtn) {
    cancelSettingsBtn.addEventListener("click", function () {
      document.getElementById("theme").value = "light";
      document.getElementById("notifications").checked = false;

      // Reset settings form
      console.log("Settings reset to default.");
    });
  }

  // Logout Modal Functionality
  const logoutButton = document.querySelector(".logout-btn");
  const logoutModal = document.getElementById("logoutModal");

  if (logoutButton) {
    logoutButton.addEventListener("click", function (event) {
      event.preventDefault(); // Prevent form submission
      openLogoutModal();
    });
  }

  function openLogoutModal() {
    logoutModal.style.display = "flex";
  }

  function closeLogoutModal() {
    logoutModal.style.display = "none";
  }

  window.confirmLogoutAction = function () {
    document.querySelector("form[action='logout.php']").submit();
  };

  window.closeLogoutModal = closeLogoutModal;
});
