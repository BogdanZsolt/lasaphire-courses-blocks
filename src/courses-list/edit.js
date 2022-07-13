import { __ } from '@wordpress/i18n';
import classnames from 'classnames';
import {
	useBlockProps,
	InspectorControls,
	BlockControls,
	__experimentalImageSizeControl as ImageSizeControl,
} from '@wordpress/block-editor';
import { useSelect } from '@wordpress/data';
import {
	Spinner,
	PanelBody,
	ToggleControl,
	ToolbarGroup,
} from '@wordpress/components';
import { list, grid } from '@wordpress/icons';
import './editor.scss';

export default function Edit( { attributes, setAttributes } ) {
	const {
		displayFeaturedImage,
		order,
		orderBy,
		featuredImageSizeSlug,
		addLinkToFeaturedImage,
		postLayout,
		displayPostContent,
		columns,
		isSlide,
	} = attributes;

	const blockProps = useBlockProps( {
		className: classnames( {
			'wp-block-ls-courses-list': true,
			'is-slide': isSlide,
			'is-grid': postLayout === 'grid' || isSlide,
			[ 'columns-5' ]: isSlide,
			[ `columns-${ columns }` ]: postLayout === 'grid' && ! isSlide,
		} ),
	} );

	const containerClass = classnames( {
		container: true,
	} );

	const imageClass = classnames( {
		'wp-block-ls-courses-list__image': true,
	} );

	// console.log( blockProps );

	const posts = useSelect( ( select ) => {
		return select( 'core' ).getEntityRecords(
			'postType',
			'ls-courses',
			{
				per_page: -1,
				_embed: 'wp:featuredmedia',
				parent: 0,
				order,
				orderby: orderBy,
			},
			[ order, orderBy ]
		);
	} );

	if ( ! posts ) {
		return <Spinner />;
	}

	if ( posts && posts.length === 0 ) {
		return "There're no results";
	}

	const getFeaturedImageDetails = ( post, size ) => {
		const image = post._embedded[ 'wp:featuredmedia' ][ 0 ];
		return {
			url: image.media_details.sizes[ size ].source_url,
			alt: image.alt_text,
		};
	};

	const onDisplayPostContentChange = ( value ) => {
		setAttributes( { displayPostContent: value } );
	};

	const layoutControls = [
		{
			icon: list,
			title: __( 'List view' ),
			onClick: () => setAttributes( { postLayout: 'list' } ),
			isActive: postLayout === 'list',
		},
		{
			icon: grid,
			title: __( 'Grid view' ),
			onClick: () => setAttributes( { postLayout: 'grid' } ),
			isActive: postLayout === 'grid',
		},
	];

	return (
		<>
			<BlockControls>
				<ToolbarGroup controls={ layoutControls } />
			</BlockControls>
			<InspectorControls>
				<PanelBody
					title={ __(
						'Post content settings',
						'lasaphire-courses-blocks'
					) }
				>
					<ToggleControl
						label={ __(
							'Post content',
							'lasaphire-courses-blocks'
						) }
						checked={ displayPostContent }
						onChange={ onDisplayPostContentChange }
					/>
					<ToggleControl
						label={ __( 'Slide', 'lasaphire-courses-blocks' ) }
						checked={ isSlide }
						onChange={ ( value ) => {
							setAttributes( { isSlide: value } );
						} }
					/>
				</PanelBody>
			</InspectorControls>
			<InspectorControls>
				<PanelBody
					title={ __(
						'Featured image settings',
						'lasaphire-courses-blocks'
					) }
				>
					<ToggleControl
						label={ __(
							'Display Featured Image',
							'lasaphire-courses-blocks'
						) }
						checked={ displayFeaturedImage }
						onChange={ ( value ) =>
							setAttributes( { displayFeaturedImage: value } )
						}
					/>
					<ToggleControl
						label={ __( 'Add link to featured image' ) }
						checked={ addLinkToFeaturedImage }
						onChange={ ( value ) =>
							setAttributes( {
								addLinkToFeaturedImage: value,
							} )
						}
					/>
				</PanelBody>
			</InspectorControls>
			<ul { ...blockProps }>
				{ posts.map( ( post ) => {
					const { url: imageSourceUrl, alt: featuredImageAlt } =
						getFeaturedImageDetails( post, featuredImageSizeSlug );
					const renderFeaturedImage =
						displayFeaturedImage && imageSourceUrl;
					const featuredImage = renderFeaturedImage && (
						<img src={ imageSourceUrl } alt={ featuredImageAlt } />
					);
					return (
						<li key={ post.id }>
							{ addLinkToFeaturedImage ? (
								<a
									className={ imageClass }
									href={ post.link }
									rel="noopener noreferrer"
								>
									{ featuredImage }
								</a>
							) : (
								featuredImage
							) }
							<div className={ containerClass }>
								<a href={ post.link } rel="noopener noreferrer">
									{ post.title.rendered }
								</a>
							</div>
						</li>
					);
				} ) }
			</ul>
		</>
	);
}
