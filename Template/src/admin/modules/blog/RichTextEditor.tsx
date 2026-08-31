'use client';

import { useEditor, EditorContent } from '@tiptap/react';
import './RichTextEditor.css';
import StarterKit from '@tiptap/starter-kit';
import Heading from '@tiptap/extension-heading';
import BulletList from '@tiptap/extension-bullet-list';
import OrderedList from '@tiptap/extension-ordered-list';
import Blockquote from '@tiptap/extension-blockquote';
import Image from '@tiptap/extension-image';
import Link from '@tiptap/extension-link';
import TextAlign from '@tiptap/extension-text-align';
import Placeholder from '@tiptap/extension-placeholder';
import {
  Bold,
  Italic,
  Underline,
  List,
  ListOrdered,
  Quote,
  Heading1,
  Heading2,
  Heading3,
  Link as LinkIcon,
  Image as ImageIcon,
  AlignLeft,
  AlignCenter,
  AlignRight,
  Undo,
  Redo
} from 'lucide-react';

type Props = {
  content: string;
  onChange: (content: string) => void;
  editable?: boolean;
};

export default function RichTextEditor({ content, onChange, editable = true }: Props) {
  const editor = useEditor({
    extensions: [
      StarterKit.configure({
        heading: false,
        bulletList: false,
        orderedList: false,
        blockquote: false,
      }),
      Heading.configure({ levels: [1, 2, 3] }),
      BulletList,
      OrderedList,
      Blockquote,
      Image.configure({
        inline: true,
        allowBase64: true,
      }),
      Link.configure({
        openOnClick: false,
        HTMLAttributes: {
          class: 'text-forest-600 underline',
        },
      }),
      TextAlign.configure({ types: ['heading', 'paragraph'] }),
      Placeholder.configure({
        placeholder: 'Start writing your blog content...',
      }),
    ],
    content,
    editable,
    onUpdate: ({ editor }) => {
      onChange(editor.getHTML());
    },
    editorProps: {
      attributes: {
        class: 'prose prose-sm sm:prose lg:prose-lg xl:prose-xl max-w-none focus:outline-none min-h-[400px] px-4 py-3',
      },
    },
  });

  if (!editor) {
    return null;
  }

  const ToolbarButton = ({
    onClick,
    active,
    icon,
    title,
  }: {
    onClick: () => void;
    active?: boolean;
    icon: React.ReactNode;
    title: string;
  }) => (
    <button
      type="button"
      onClick={onClick}
      title={title}
      className={`p-2 rounded-lg transition-colors ${
        active ? 'bg-forest-100 text-forest-700' : 'text-foreground/60 hover:bg-earth-100 hover:text-foreground'
      }`}
    >
      {icon}
    </button>
  );

  const addImage = () => {
    const url = window.prompt('Enter image URL:');
    if (url) {
      editor.chain().focus().setImage({ src: url }).run();
    }
  };

  const addLink = () => {
    const url = window.prompt('Enter URL:');
    if (url) {
      editor.chain().focus().setLink({ href: url }).run();
    }
  };

  return (
    <div className="border border-earth-200 rounded-xl overflow-hidden">
      {/* Toolbar */}
      <div className="bg-earth-50 border-b border-earth-200 p-2 flex flex-wrap gap-1">
        <ToolbarButton
          onClick={() => editor.chain().focus().toggleBold().run()}
          active={editor.isActive('bold')}
          icon={<Bold className="w-4 h-4" />}
          title="Bold"
        />
        <ToolbarButton
          onClick={() => editor.chain().focus().toggleItalic().run()}
          active={editor.isActive('italic')}
          icon={<Italic className="w-4 h-4" />}
          title="Italic"
        />
        <ToolbarButton
          onClick={() => editor.chain().focus().toggleUnderline().run()}
          active={editor.isActive('underline')}
          icon={<Underline className="w-4 h-4" />}
          title="Underline"
        />
        
        <span className="w-px h-6 bg-earth-300 mx-1" />
        
        <ToolbarButton
          onClick={() => editor.chain().focus().toggleHeading({ level: 1 }).run()}
          active={editor.isActive('heading', { level: 1 })}
          icon={<Heading1 className="w-4 h-4" />}
          title="Heading 1"
        />
        <ToolbarButton
          onClick={() => editor.chain().focus().toggleHeading({ level: 2 }).run()}
          active={editor.isActive('heading', { level: 2 })}
          icon={<Heading2 className="w-4 h-4" />}
          title="Heading 2"
        />
        <ToolbarButton
          onClick={() => editor.chain().focus().toggleHeading({ level: 3 }).run()}
          active={editor.isActive('heading', { level: 3 })}
          icon={<Heading3 className="w-4 h-4" />}
          title="Heading 3"
        />
        
        <span className="w-px h-6 bg-earth-300 mx-1" />
        
        <ToolbarButton
          onClick={() => editor.chain().focus().toggleBulletList().run()}
          active={editor.isActive('bulletList')}
          icon={<List className="w-4 h-4" />}
          title="Bullet List"
        />
        <ToolbarButton
          onClick={() => editor.chain().focus().toggleOrderedList().run()}
          active={editor.isActive('orderedList')}
          icon={<ListOrdered className="w-4 h-4" />}
          title="Ordered List"
        />
        <ToolbarButton
          onClick={() => editor.chain().focus().toggleBlockquote().run()}
          active={editor.isActive('blockquote')}
          icon={<Quote className="w-4 h-4" />}
          title="Quote"
        />
        
        <span className="w-px h-6 bg-earth-300 mx-1" />
        
        <ToolbarButton
          onClick={() => editor.chain().focus().setTextAlign('left').run()}
          active={editor.isActive({ textAlign: 'left' })}
          icon={<AlignLeft className="w-4 h-4" />}
          title="Align Left"
        />
        <ToolbarButton
          onClick={() => editor.chain().focus().setTextAlign('center').run()}
          active={editor.isActive({ textAlign: 'center' })}
          icon={<AlignCenter className="w-4 h-4" />}
          title="Align Center"
        />
        <ToolbarButton
          onClick={() => editor.chain().focus().setTextAlign('right').run()}
          active={editor.isActive({ textAlign: 'right' })}
          icon={<AlignRight className="w-4 h-4" />}
          title="Align Right"
        />
        
        <span className="w-px h-6 bg-earth-300 mx-1" />
        
        <ToolbarButton
          onClick={addLink}
          icon={<LinkIcon className="w-4 h-4" />}
          title="Add Link"
        />
        <ToolbarButton
          onClick={addImage}
          icon={<ImageIcon className="w-4 h-4" />}
          title="Add Image"
        />
        
        <span className="w-px h-6 bg-earth-300 mx-1" />
        
        <ToolbarButton
          onClick={() => editor.chain().focus().undo().run()}
          icon={<Undo className="w-4 h-4" />}
          title="Undo"
        />
        <ToolbarButton
          onClick={() => editor.chain().focus().redo().run()}
          icon={<Redo className="w-4 h-4" />}
          title="Redo"
        />
      </div>
      
      {/* Editor */}
      <EditorContent editor={editor} />
    </div>
  );
}
