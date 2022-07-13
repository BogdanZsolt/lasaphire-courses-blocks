import { __ } from '@wordpress/i18n';
import { useSelect } from '@wordpress/data';
import { useRef, useEffect } from '@wordpress/element';
import { useEntityProp } from '@wordpress/core-data';
import { ToolbarButton, withNotices } from '@wordpress/components';
import {
	useBlockProps,
	MediaPlaceholder,
	BlockControls,
	MediaReplaceFlow,
} from '@wordpress/block-editor';
import './editor.scss';

function Edit( noticeOperations, noticeUI ) {
	const postType = useSelect( ( select ) => {
		return select( 'core/editor' ).getCurrentPostType();
	}, [] );
	const videoPlayer = useRef();
	const [ meta, setMeta ] = useEntityProp( 'postType', postType, 'meta' );
	const videoUrl = meta._ls_course_video;
	const ALLOWED_MEDIA_TYPES = [ 'video' ];

	useEffect( () => {
		// Placeholder may be rendered.
		if ( videoPlayer.current ) {
			videoPlayer.current.load();
		}
	}, [] );

	const onSelectVideo = ( value ) => {
		setMeta( { ...meta, _ls_course_video: value.url } );
	};

	const onSelectURL = ( value ) => {
		setMeta( { ...meta, _ls_course_video: value } );
	};

	const onRemoveVideo = () => {
		setMeta( { ...meta, _ls_course_video: '' } );
	};

	const onUploadError = ( message ) => {
		noticeOperations.removeAllNotices();
		noticeOperations.createErrorNotice( message );
	};

	return (
		<>
			{ videoUrl && (
				<BlockControls>
					<MediaReplaceFlow
						mediaURL={ videoUrl }
						allowedTypes={ ALLOWED_MEDIA_TYPES }
						accept="video/*"
						onSelect={ onSelectVideo }
						onSelectURL={ onSelectURL }
						onError={ onUploadError }
						notices={ noticeUI }
					/>
					<ToolbarButton onClick={ onRemoveVideo }>
						{ __( 'Remove', 'ls-courses' ) }
					</ToolbarButton>
				</BlockControls>
			) }
			<figure { ...useBlockProps() }>
				{ videoUrl && (
					<video
						width="100%"
						src={ videoUrl }
						controls
						ref={ videoPlayer }
					/>
				) }
				{ ! videoUrl && (
					<MediaPlaceholder
						icon="admin-users"
						accept="video/*"
						allowedTypes={ ALLOWED_MEDIA_TYPES }
						onSelect={ onSelectVideo }
						onSelectURL={ onSelectURL }
					/>
				) }
			</figure>
		</>
	);
}

export default withNotices( Edit );
