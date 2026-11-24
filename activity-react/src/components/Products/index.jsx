import React from 'react';
import ProductCatalog from './ProductCatalog';

// Importing './components/Products' resolves to this index file,
// so the router can render the catalog page from a single import
export default function Products() {
  return <ProductCatalog />;
}
