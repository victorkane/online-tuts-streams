import { __ } from "@wordpress/i18n";
import { compose } from "@wordpress/compose";
import { withSelect, withDispatch } from "@wordpress/data";
import { PluginDocumentSettingPanel } from "@wordpress/edit-post";
import { PanelRow, TextControl, DateTimePicker } from "@wordpress/components";

const MetaBox = ({ postType, metaFields, setMetaFields }) => {
	if ("post" !== postType) return null;

	return (
		<PluginDocumentSettingPanel
			title={__("Book details")}
			icon="book-alt"
			initialOpen={false}
		>
			<PanelRow>
				<TextControl
					value={metaFields._meta_fields_book_title}
					label={__("Title")}
					onChange={(value) =>
						setMetaFields({ _meta_fields_book_title: value })
					}
				/>
			</PanelRow>
			<PanelRow>
				<TextControl
					value={metaFields._meta_fields_book_author}
					label={__("Author")}
					onChange={(value) =>
						setMetaFields({ _meta_fields_book_author: value })
					}
				/>
			</PanelRow>
			<PanelRow>
				<TextControl
					value={metaFields._meta_fields_book_publisher}
					label={__("Publisher")}
					onChange={(value) =>
						setMetaFields({ _meta_fields_book_publisher: value })
					}
				/>
			</PanelRow>
			<PanelRow>
				<DateTimePicker
					currentDate={metaFields._meta_fields_book_date}
					onChange={(newDate) =>
						setMetaFields({ _meta_fields_book_date: newDate })
					}
					__nextRemoveHelpButton
					__nextRemoveResetButton
				/>
			</PanelRow>
		</PluginDocumentSettingPanel>
	);
};

const applyWithSelect = withSelect((select) => {
	return {
		metaFields: select("core/editor").getEditedPostAttribute("meta"),
		postType: select("core/editor").getCurrentPostType(),
	};
});

const applyWithDispatch = withDispatch((dispatch) => {
	return {
		setMetaFields(newValue) {
			dispatch("core/editor").editPost({ meta: newValue });
		},
	};
});

export default compose([applyWithSelect, applyWithDispatch])(MetaBox);
