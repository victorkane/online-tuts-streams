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
import { useBlockProps, RichText } from "@wordpress/block-editor";

/**
 * Lets webpack process CSS, SASS or SCSS files referenced in JavaScript files.
 * Those files can contain any CSS code that gets applied to the editor.
 *
 * @see https://www.npmjs.com/package/@wordpress/scripts#using-css
 */
import "./editor.scss";

/**
 * Gain access to the post meta for the current post
 */
import { useEntityProp } from "@wordpress/core-data";

/**
 * The edit function describes the structure of your block in the context of the
 * editor. This represents what the editor will render when the block is used.
 *
 * @see https://developer.wordpress.org/block-editor/reference-guides/block-api/block-edit-save/#edit
 *
 * @return {WPElement} Element to render.
 */
export default function Edit({
	attributes: { authorName, authorURL },
	setAttributes,
	context: { postType, postId },
}) {
	const [meta, updateMeta] = useEntityProp(
		"postType",
		"product",
		"meta",
		postId
	);
	const { testimonial } = meta;
	return (
		<blockquote {...useBlockProps()}>
			<RichText
				placeholder={__("Testimonial goes here", "tutorial")}
				tagName="p"
				value={testimonial}
				onChange={(newTestimonialContent) =>
					updateMeta({
						...meta,
						testimonial: newTestimonialContent,
					})
				}
			/>
			<cite>
				<RichText
					tagName="span"
					placeholder={__("Author name", "tutorial")}
					allowedFormats={[]}
					disableLineBreaks
					value={authorName}
					onChange={(newAuthorName) =>
						setAttributes({ authorName: newAuthorName })
					}
				/>
				<br />
				<span>
					<RichText
						tagName="a"
						placeholder={__("Author URL", "tutorial")}
						allowedFormats={[]}
						disableLineBreaks
						value={authorURL}
						onChange={(newAuthorURL) =>
							setAttributes({ authorURL: newAuthorURL })
						}
					/>
				</span>
			</cite>
		</blockquote>
	);
}
