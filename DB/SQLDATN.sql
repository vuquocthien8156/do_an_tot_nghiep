Create database DatHangThucUong

Create table NguoiDung(
	id int primary key ,
	ten text,
	sdt text,
	gioi_tinh int,
	diem_tich int,
	ngay_sinh date,
	dia_chi text,
	facebook_id text,
	email text,
	mat_khau text,
	anh_dai_dien text,
	da_xoa int
)


Create table VaiTro(
	ma_vai_tro  int primary key,
	ten_vai_tro  text,
	trang_thai_ int
)

Create table PhanQuyen(
	ma_phan_quyen int primary key ,
	tai_khoan int foreign key (tai_khoan) references NguoiDung(id),
	quyen_cho_phep int foreign key (quyen_cho_phep) references VaiTro(ma_vai_tro),
	da_xoa int ,
)

Create table LoaiSanPham(
	ma_loai_sp int primary key ,
	ten_loai_sp text,
	loai_chinh int,
	da_xoa int
)


Create table SanPham(
	ma_so  int primary key,
	ma_chu  text,
	ten  text,
	gia_san_pham  bigint,
	gia_vua  bigint,  
	gia_lon  bigint, 
	loai_sp  int foreign key (loai_sp) references LoaiSanPham(ma_loai_sp), 
	ngay_ra_mat  text,
	hinh_san_pham  text,
	mo_ta text,
	da_xoa int
)

Create table SanPhamYeuThich(
	ma_yeu_thich  int primary key,
	ma_san_pham int foreign key (ma_san_pham) references SanPham(ma_so),
	ma_khach_hang int foreign key (ma_khach_hang) references NguoiDung(id),
	thich int

)

Create table GioHang(
	ma_gio_hang Int primary key,
	ma_khach_hang Int foreign key (ma_khach_hang) references NguoiDung(id),
	ma_san_pham Int foreign key (ma_san_pham) references SanPham(ma_so),
	kich_co text ,
	so_luong int,
	ghi_chu text,
)

Create table ChiTietGioHang(
	ma_gio_hang Int foreign key (ma_gio_hang) references GioHang (ma_gio_hang),
	ma_san_pham Int foreign key (ma_san_pham) references SanPham (ma_so),
	so_luong Int,
	primary key (ma_gio_hang , ma_san_pham)
)


Create table TinTuc(
	ma_tin_tuc int primary key,
	ten_tin_tuc text,
	noi_dung text,
	ngay_dang text,
	hinh_tin_tuc text,
	ngay_tao text,
	tai_khoan_tao int foreign key (tai_khoan_tao) references NguoiDung(id),
	da_xoa  int
)

Create table KhuyenMai(
	ma_khuyen_mai int primary key,
	ma_code text,
	hinh_anh text,
	ten_khuyen_mai text,
	mo_ta text,
	so_phan_tram int,
	so_tien bigint,
	so_sp_qui_dinh int,
	so_sp_tang_kem int,
	gioi_han_so_code int,
	ngay_bat_dau text,
	ngay_ket_thuc text,
	so_tien_qui_dinh_toi_thieu int,
	hien_slider int,
	ma_san_pham int,
	da_xoa int
)

Create table HinhAnh(
	ma_hinh int ,
	object_id int,
	kieu int ,
	url text,
	da_xoa int,
	primary key(ma_hinh , object_id , kieu)
)

Create table TrangThaiDonHang(
	ma_trang_thai int primary key,
	ten_trang_thai text,
	da_xoa int
)

Create table Donhang(
	ma_don_hang int primary key,
	ma_chu text,
	thong_tin_giao_hang text,
	ma_khach_hang int,
	ngay_lap text,
	khuyen_mai int foreign key (khuyen_mai) references KhuyenMai (ma_khuyen_mai),
	phi_ship int,
	phuong_thuc_thanh_toan int,
	tong_tien int,
	tong_tien_khuyen_mai text,
	ghi_chu  text,
	da_xoa  int
)

Create table ChiTietDonHang(
	ma_chi_tiet int primary key,
	ma_don_hang int foreign key (ma_don_hang) references DonHang(ma_don_hang),
	ma_san_pham int foreign key (ma_san_pham) references SanPham(ma_so),
	so_luong int,
	don_gia bigint,
	kich_co text,
	gia_khuyen_mai bigint,
	thanh_tien bigint,
	ghi_chu text
)


Create table ChiTietThucUong(
	ma_chi_tiet int foreign key (ma_chi_tiet) references ChiTietDonHang(ma_chi_tiet) ,
	ma_san_pham int foreign key (ma_san_pham) references SanPham(ma_so),
	don_gia bigint,
	so_luong int,
	primary key(ma_chi_tiet , ma_san_pham)
)

Create table ChiTietTrangThaiDonHang(
	ma_don_hang int foreign key (ma_don_hang) references DonHang(ma_don_hang),
	trang_thai int foreign key (trang_thai) references TrangThaiDonHang(ma_trang_thai),
	thoi_gian text,
	da_xoa int,
	primary key(ma_don_hang , trang_thai)
)

Create table LichSuDiem(
	ma_tai_khoan int primary key,
	ma_don_hang int foreign key (ma_don_hang) references DonHang(ma_don_hang),
	so_diem int,
	hinh_thuc int,
	thoi_gian datetime,
	da_xoa int
)

Create table ChiNhanh(
	ma_chi_nhanh  int primary key,
	ten_chi_nhanh text,
	so_dien_thoai text,
	dia_chi text,
	latiture text,
	longitude text,
	ngay_khai_truong text,
	gio_mo_cua text,
	gio_dong_cua text,
	ma_khu_vuc int foreign key (ma_khu_vuc) references Khu_Vuc(ma_khu_vuc),
	da_xoa int
)

Create table Khu_Vuc(
	ma_khu_vuc int primary key,
	ten_khu_vuc text,
	da_xoa int
)

Create table SoDiaChi(
	ma_thong_tin int primary key,
	tai_khoan int foreign key (tai_khoan) references NguoiDung(id),
	ten_nguoi_nhan text,
	so_dien_thoai text,
	dia_chi text,
	chinh int,
	da_xoa int
)

Create table DanhGia(
	ma_danh_gia int primary key,
 	tai_khoan int foreign key (tai_khoan) references NguoiDung(id),
 	san_pham int foreign key (san_pham) references SanPham(ma_so),
	so_diem int,
	tieu_de text,
 	noi_dung text,
 	thoi_gian text,
	duyet int,
 	da_xoa int
)

Create table DanhGiaCon(
	ma_danh_gia_con int primary key,
	ma_danh_gia int foreign key (ma_danh_gia) references DanhGia(ma_danh_gia),
	tai_khoan int foreign key (tai_khoan) references NguoiDung(id),
	noi_dung text,
	thoi_gian text,
	duyet int,
	da_xoa int
)

Create table CamOnDanhGia(
	ma_danh_gia Int,
	tai_khoan Int foreign key (tai_khoan) references NguoiDung(id) ,
	primary key(ma_danh_gia , tai_khoan)
)
