export class Product {
  id: number;
  imglink: string;
  name: string;
  price: number;
  description: string;

  constructor() {
    this.id = 0;
    this.imglink = '';
    this.name = '';
    this.price = 0;
    this.description = '';
  }
}
