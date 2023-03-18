/**
 * Retrieves the translation of text.
 *
 * @see https://developer.wordpress.org/block-editor/reference-guides/packages/packages-i18n/
 */
import { __ } from "@wordpress/i18n";

/**
 * React hook that is used to mark the block wrapper element.
 * It provides all the necessary props like the class name.
 *
 * @see https://developer.wordpress.org/block-editor/reference-guides/packages/packages-block-editor/#useblockprops
 */
import {
	useBlockProps,
	InspectorControls,
	RichText,
} from "@wordpress/block-editor";

import { useSelect } from "@wordpress/data";

import { useEntityProp } from "@wordpress/core-data";

import {
	TextControl,
	DatePicker,
	TimePicker,
	PanelBody,
	PanelRow,
} from "@wordpress/components";

/**
 * Lets webpack process CSS, SASS or SCSS files referenced in JavaScript files.
 * Those files can contain any CSS code that gets applied to the editor.
 *
 * @see https://www.npmjs.com/package/@wordpress/scripts#using-css
 */
import "./editor.scss";

/**
 * The edit function describes the structure of your block in the context of the
 * editor. This represents what the editor will render when the block is used.
 *
 * @see https://developer.wordpress.org/block-editor/reference-guides/block-api/block-edit-save/#edit
 *
 * @return {WPElement} Element to render.
 */
export default function Edit() {
	const blockProps = useBlockProps();

	const postType = useSelect(
		(select) => select("core/editor").getCurrentPostType(),
		[]
	);

	const [meta, setMeta] = useEntityProp("postType", postType, "meta");

	const bookTitle = meta["_meta_fields_book_title"];
	const bookAuthor = meta["_meta_fields_book_author"];
	const bookPublisher = meta["_meta_fields_book_publisher"];
	const bookDate = meta["_meta_fields_book_date"];

	const updateBookTitleMetaValue = (newValue) => {
		setMeta({ ...meta, _meta_fields_book_title: newValue });
	};

	const updateBookAuthorMetaValue = (newValue) => {
		setMeta({ ...meta, _meta_fields_book_author: newValue });
	};

	const updateBookPublisherMetaValue = (newValue) => {
		setMeta({ ...meta, _meta_fields_book_publisher: newValue });
	};

	return (
		<>
			<InspectorControls>
				<PanelBody title={__("Book Details")} initialOpen={true}>
					<PanelRow>
						<fieldset>
							<TextControl
								label={__("Book title")}
								value={bookTitle}
								onChange={updateBookTitleMetaValue}
							/>
						</fieldset>
					</PanelRow>
					<PanelRow>
						<fieldset>
							<TextControl
								label={__("Book author")}
								value={bookAuthor}
								onChange={updateBookAuthorMetaValue}
							/>
						</fieldset>
					</PanelRow>
					<PanelRow>
						<fieldset>
							<TextControl
								label={__("Publisher")}
								value={bookPublisher}
								onChange={updateBookPublisherMetaValue}
							/>
						</fieldset>
					</PanelRow>
					<PanelRow>
						<fieldset>
							<DatePicker
								currentDate={bookDate}
								onChange={(newValue) =>
									setMeta({ ...meta, _meta_fields_book_date: newValue })
								}
								__nextRemoveHelpButton
								__nextRemoveResetButton
							/>
						</fieldset>
					</PanelRow>
				</PanelBody>
			</InspectorControls>
			<div {...blockProps}>
				<RichText
					tagName="h3"
					onChange={updateBookTitleMetaValue}
					allowedFormats={["core/bold", "core/italic"]}
					value={bookTitle}
					placeholder={__("Write your text...")}
				/>
				<TextControl
					label={__("Book author")}
					value={bookAuthor}
					onChange={updateBookAuthorMetaValue}
				/>
				<TextControl
					label={__("Book publisher")}
					value={bookPublisher}
					onChange={updateBookPublisherMetaValue}
				/>
				{/*
                    https://github.com/WordPress/gutenberg/tree/trunk/packages/components/src/date-time
                    https://developer.wordpress.org/block-editor/reference-guides/components/date-time/
                    */}
				<TimePicker
					currentTime={bookDate}
					onChange={(newValue) =>
						setMeta({ ...meta, _meta_fields_book_date: newValue })
					}
					__nextRemoveHelpButton
					__nextRemoveResetButton
				/>
			</div>
		</>
	);
}
