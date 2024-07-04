/**
 * Registers a new block provided a unique name and an object defining its behavior.
 *
 * @see https://developer.wordpress.org/block-editor/reference-guides/block-api/block-registration/
 */
import { registerBlockType } from '@wordpress/blocks';

/**
 * Lets webpack process CSS, SASS or SCSS files referenced in JavaScript files.
 * All files containing `style` keyword are bundled together. The code used
 * gets applied both to the front of your site and to the editor.
 *
 * @see https://www.npmjs.com/package/@wordpress/scripts#using-css
 */
import './style.scss';

/**
 * Internal dependencies
 */
import Edit from './edit';
import metadata from './block.json';

/**
 * Every block starts by registering a new block type definition.
 *
 * @see https://developer.wordpress.org/block-editor/reference-guides/block-api/block-registration/
 */
registerBlockType(metadata.name, {
	/**
	 * @see ./edit.js
	 */
	icon: {
		src: (
			<>
				{/* Uploaded to: SVG Repo, www.svgrepo.com, Generator: SVG Repo Mixer Tools */}
				<svg
					fill="#000000"
					height="24px"
					width="24px"
					version="1.1"
					id="Icons"
					xmlns="http://www.w3.org/2000/svg"
					xmlnsXlink="http://www.w3.org/1999/xlink"
					viewBox="-3.2 -3.2 38.40 38.40"
					xmlSpace="preserve"
					stroke="#000000"
					strokeWidth="0.00032"
					transform="matrix(1, 0, 0, 1, 0, 0)rotate(0)"
				>
					<g id="SVGRepo_bgCarrier" strokeWidth={0} />
					<g
						id="SVGRepo_tracerCarrier"
						strokeLinecap="round"
						strokeLinejoin="round"
						stroke="#CCCCCC"
						strokeWidth="0.192"
					/>
					<g id="SVGRepo_iconCarrier">
						{" "}
						<path d="M28,12H14H4c-2.2,0-4,1.8-4,4s1.8,4,4,4h10h14c2.2,0,4-1.8,4-4S30.2,12,28,12z M4,18c-1.1,0-2-0.9-2-2s0.9-2,2-2h10 c1.1,0,2,0.9,2,2s-0.9,2-2,2H4z" />{" "}
					</g>
				</svg>

			</>
		),
	},
	edit: Edit,
});

