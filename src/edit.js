import { useState, useEffect } from '@wordpress/element';
import { InspectorControls, useBlockProps } from '@wordpress/block-editor';
import apiFetch from '@wordpress/api-fetch';
import {
	PanelBody,
	CustomSelectControl,
	SelectControl,
} from '@wordpress/components';
import './editor.scss';

export default function Edit( { attributes, setAttributes } ) {
	const { selectedCategory, faqCount } = attributes;
	const [ categories, setCategories ] = useState( [] );
	const [ faqs, setFaqs ] = useState( [] );
	const [ expandedIndex, setExpandedIndex ] = useState( null );

	// Fetch categories when the component mounts
	useEffect( () => {
		apiFetch( { path: '/wp/v2/categories?per_page=-1' } ).then(
			( categories ) => setCategories( categories )
		);
	}, [] );

	// Fetch FAQs when category changes
	useEffect( () => {
		if ( selectedCategory ) {
			apiFetch( {
				path: `/wp/v2/faq?categories=${ selectedCategory }`,
			} ).then( ( faqs ) => setFaqs( faqs.slice( 0, faqCount ) ) );
		}
	}, [ selectedCategory, faqCount ] );

	// Handle category selection
	const handleCategoryChange = ( option ) => {
		const categoryId = option?.selectedItem?.key || 0;
		setAttributes( { selectedCategory: categoryId } );
	};

	// Toggle accordion open/close state
	const toggleAccordion = ( index ) => {
		setExpandedIndex( expandedIndex === index ? null : index );
	};

	const blockProps = useBlockProps();

	return (
		<div { ...blockProps }>
			<InspectorControls>
				<PanelBody title="Select FAQ Category" initialOpen={ true }>
					<CustomSelectControl
						options={ categories.map( ( cat ) => ( {
							key: cat.id,
							name: cat.name,
						} ) ) }
						onChange={ handleCategoryChange }
						value={ categories.find(
							( cat ) => cat.id === selectedCategory
						) }
					/>
				</PanelBody>

				<PanelBody title="Number of FAQs to Show" initialOpen={ false }>
					<SelectControl
						value={ faqCount }
						options={ [
							{ label: '3', value: 3 },
							{ label: '4', value: 4 },
							{ label: '5', value: 5 },
						] }
						onChange={ ( value ) =>
							setAttributes( {
								faqCount: parseInt( value, 10 ),
							} )
						}
					/>
				</PanelBody>
			</InspectorControls>

			<div className="faq-list">
				{ faqs.length ? (
					faqs.map( ( faq, index ) => (
						<div className="faq-item" key={ faq.id }>
							<div
								className="faq-question"
								onClick={ () => toggleAccordion( index ) }
							>
								{ faq.title.rendered }
								<span className="faq-toggle">
									{ expandedIndex === index ? '-' : '+' }
								</span>
							</div>
							{ expandedIndex === index && (
								<div
									className="faq-answer"
									dangerouslySetInnerHTML={ {
										__html: faq.content.rendered,
									} }
								/>
							) }
						</div>
					) )
				) : (
					<p style={ { color: 'rgb(51, 51, 51)' } }>
						No FAQs found for the selected category.
					</p>
				) }
			</div>
		</div>
	);
}
