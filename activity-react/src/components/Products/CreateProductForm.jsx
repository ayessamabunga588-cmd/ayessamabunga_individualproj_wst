import React, { useState } from 'react';
import api from '../../api/axios';

export default function CreateProductForm() {
  const [formData, setFormData] = useState({
    code: '',
    name: '',
    description: '',
    manufacturer: '',
    price: '',
    cost: '',
    inventory_quantity: '',
  });
  const [photo, setPhoto] = useState(null);
  const [preview, setPreview] = useState('');
  const [isDragActive, setIsDragActive] = useState(false);
  const [message, setMessage] = useState('');

  const handleInputChange = (e) => setFormData({ ...formData, [e.target.name]: e.target.value });

  const handleDrag = (e) => {
    e.preventDefault();
    setIsDragActive(e.type === 'dragenter' || e.type === 'dragover');
  };

  const handleDrop = (e) => {
    e.preventDefault();
    setIsDragActive(false);
    if (e.dataTransfer.files && e.dataTransfer.files[0]) {
      setPhoto(e.dataTransfer.files[0]);
      setPreview(URL.createObjectURL(e.dataTransfer.files[0]));
    }
  };

  const handleSubmit = async (e) => {
    e.preventDefault();
    const data = new FormData();
    Object.keys(formData).forEach((key) => data.append(key, formData[key]));
    if (photo) data.append('photo', photo);

    try {
      const response = await api.post('/products', data, {
        headers: { 'Content-Type': 'multipart/form-data' },
      });
      setMessage(response.data.message);
    } catch (error) {
      const responseData = error.response?.data;
      const firstFieldError = responseData?.errors ? Object.values(responseData.errors)[0]?.[0] : null;
      setMessage(firstFieldError || responseData?.message || 'Something went wrong.');
    }
  };

  return (
    <div style={{ maxWidth: '500px', margin: '20px auto' }}>
      <h2>Create Protected Product</h2>
      {message && <p>{message}</p>}

      <form onSubmit={handleSubmit} style={{ display: 'flex', flexDirection: 'column', gap: '12px' }}>
        <input type="text" name="code" placeholder="Code (12 Char)" onChange={handleInputChange} required />
        <input type="text" name="name" placeholder="Name" onChange={handleInputChange} required />
        <textarea name="description" placeholder="Description" onChange={handleInputChange} required />
        <input type="text" name="manufacturer" placeholder="Manufacturer" onChange={handleInputChange} required />
        <input type="number" step="0.01" name="price" placeholder="Price" onChange={handleInputChange} required />
        <input type="number" step="0.01" name="cost" placeholder="Cost" onChange={handleInputChange} required />
        <input type="number" name="inventory_quantity" placeholder="Quantity" onChange={handleInputChange} required />

        <div
          onDragEnter={handleDrag}
          onDragOver={handleDrag}
          onDragLeave={handleDrag}
          onDrop={handleDrop}
          style={{ border: '2px dashed #ccc', padding: '20px', textAlign: 'center', background: isDragActive ? '#f0f8ff' : '#fafafa' }}
        >
          <p>Drag &amp; drop product picture here or use the file input below.</p>
          <input
            type="file"
            accept="image/*"
            onChange={(e) => {
              setPhoto(e.target.files[0]);
              setPreview(URL.createObjectURL(e.target.files[0]));
            }}
          />
          {preview && <img src={preview} alt="Preview" style={{ maxWidth: '100px', marginTop: '10px' }} />}
        </div>

        <button type="submit" style={{ padding: '10px', background: '#2563eb', color: '#fff', border: 'none' }}>
          Upload Product
        </button>
      </form>
    </div>
  );
}
