function b1nT_pop_up_dialog(b1nT_arg_config) {
	var b1nT_dialog_box = this;
	b1nT_dialog_box.config = b1nT_arg_config;

	this._b1nT_init = function() {
		if(b1nT_dialog_box.config.shield_div && b1nT_dialog_box.config.dialog_box_div) {
			b1nT_dialog_box.config.shield_div.onclick = function() { b1nT_dialog_box.b1nT_close_dialog(); }

			//create content of dialog box
			var b1nT_header_div = document.createElement("div");
			var b1nT_first_child = document.createElement("div");
			var b1nT_second_child = document.createElement("div");
			var b1nT_span = document.createElement("span");
			b1nT_span.innerHTML = "X";

			b1nT_second_child.appendChild(b1nT_span);

			b1nT_second_child.onclick = function() {
				b1nT_dialog_box.b1nT_close_dialog();
			}

			b1nT_header_div.appendChild(b1nT_first_child);
			b1nT_header_div.appendChild(b1nT_second_child);

			var b1nT_content_div = document.createElement("div");

			b1nT_dialog_box.config.dialog_box_div.appendChild(b1nT_header_div);
			b1nT_dialog_box.config.dialog_box_div.appendChild(b1nT_content_div);

			//keep track of some divs
			b1nT_dialog_box.header_div = b1nT_first_child;
			b1nT_dialog_box.content_div = b1nT_content_div;
		}
	}

	this.b1nT_open_dialog = function(b1nT_header, b1nT_content) {
		if(b1nT_dialog_box.header_div && b1nT_dialog_box.content_div) {
			//clear
			b1nT_dialog_box.header_div.innerHTML = "";
			b1nT_dialog_box.content_div.innerHTML = "";

			//fill
			b1nT_dialog_box.header_div.innerHTML = b1nT_header;
			b1nT_dialog_box.content_div.innerHTML = b1nT_content;

			//display
			b1nT_dialog_box.config.shield_div.style.display = "block";
			b1nT_dialog_box.config.dialog_box_div.style.display = "block";
		}
	}

	this.b1nT_close_dialog = function() {
		if(b1nT_dialog_box.header_div && b1nT_dialog_box.content_div) {
			//hide
			b1nT_dialog_box.config.dialog_box_div.style.display = "none";
			b1nT_dialog_box.config.shield_div.style.display = "none";

			//clear
			b1nT_dialog_box.header_div.innerHTML = "";
			b1nT_dialog_box.content_div.innerHTML = "";
		}
	}

	this._b1nT_init();
	return this;
}