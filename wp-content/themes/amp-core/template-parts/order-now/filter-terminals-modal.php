<div id="TerminalFilterModal" class="modal">
	<div class="modal__content">
		<span class="modal__close js-modal__close">&times;</span>
		<header>Please Select a Terminal to Order</header>
		<p>Termianls..</p>
	</div>
</div>


<script>
  let TerminalFilterModal = document.getElementById("TerminalFilterModal");
  let TerminalFilterModalLink = document.getElementById("TerminalFilterModalLink");
  let TerminalFilterModalClose = document.getElementsByClassName("js-modal__close")[0];

  TerminalFilterModalLink.onclick = function () {
    TerminalFilterModal.style.display = "block";
  }

  TerminalFilterModalClose.onclick = function () {
    TerminalFilterModal.style.display = "none";
  }

  window.onclick = function (event) {
    if (event.target === TerminalFilterModal) {
      TerminalFilterModal.style.display = "none";
    }
  }
</script>
