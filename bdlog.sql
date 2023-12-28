alter table catalogoReportes
add ReportePesado int 

CREATE TABLE Inputs (
    idInput INT PRIMARY KEY IDENTITY,
    nombreInput VARCHAR(255),
    tipo VARCHAR(255),
    valor text,
    valorAutocomplete VARCHAR(50),
    valorID varchar(255)
    valoresSelect text,
    textoSelect text,
    checked int,
    informacionAdicional text,
    hora time
);


CREATE TABLE ReportesInputs (
    idInput INT,
    id_reporte INT,
    FOREIGN KEY (idInput) REFERENCES Inputs(idInput),
    FOREIGN KEY (id_reporte) REFERENCES catalogoReportes(id_reporte),
    PRIMARY KEY (idInput, id_reporte)
);


CREATE PROCEDURE insertCatalogoReporte
@descripcion VARCHAR(255),
@sp_reporte VARCHAR(255),
@activo INT,
@Pesado int
AS
BEGIN
    DECLARE @InsertedID INT;

    INSERT INTO catalogoReportes
    OUTPUT INSERTED.id_reporte -- Esta línea devuelve la ID asignada
    VALUES (@descripcion, @sp_reporte, @activo, @Pesado);

    -- Guarda la ID asignada en la variable
    SET @InsertedID = SCOPE_IDENTITY();

    -- Puedes hacer algo con la ID, como devolverla o usarla en otra parte del procedimiento

    SELECT @InsertedID AS 'ID'; -- Esta línea devuelve la ID asignada
END;

CREATE PROCEDURE unirReporteInput
	@idInput int,
	@idReporte int,
	@orden int
AS
BEGIN
	insert into ReportesInputs values (@idInput, @idReporte, @orden)
END;


CREATE PROCEDURE unirReporteInputPosicion
    @idInput INT,
    @idReporte INT,
    @posicion INT
AS
BEGIN
    -- Verificar si ya existe una relación entre idInput e idReporte
    IF EXISTS (SELECT 1 FROM ReportesInputs WHERE idInput = @idInput AND id_reporte = @idReporte)
    BEGIN
        -- Si existe, verificar si la posición es diferente
        IF NOT EXISTS (SELECT 1 FROM ReportesInputs WHERE idInput = @idInput AND id_reporte = @idReporte AND orden = @posicion)
        BEGIN
            -- Si la posición es diferente, actualizarla
            UPDATE ReportesInputs
            SET orden = @posicion
            WHERE idInput = @idInput AND id_reporte = @idReporte;
        END
        -- Si la posición es la misma, no hacer nada
    END
    ELSE
    BEGIN
        -- Si no existe una relación, insertar una nueva
        INSERT INTO ReportesInputs (idInput, id_reporte, orden)
        VALUES (@idInput, @idReporte, @posicion);
    END
END;

CREATE procedure selectInputsFueraReporte
@idReportes int
as
begin
SELECT Inputs.*
FROM Inputs
LEFT JOIN ReportesInputs ON Inputs.idInput = ReportesInputs.idInput AND ReportesInputs.id_reporte = @idReportes
WHERE ReportesInputs.idInput IS NULL;
end

CREATE PROCEDURE eliminarReporteInputPosicion
    @idInput INT,
    @idReporte INT
AS
BEGIN
    -- Verificar si ya existe una relación entre idInput e idReporte
   Delete ReportesInputs where idInput = @idInput AND id_reporte = @idReporte;
END;

CREATE PROCEDURE autoCompleteReporteador
@filtro NVARCHAR(100) = NULL,
@tabla NVARCHAR(100) = NULL,
@id NVARCHAR(100) = NULL,
@valor NVARCHAR(100) = NULL
AS
BEGIN
    DECLARE @sql NVARCHAR(MAX)
	

    SET @sql = '
        SELECT ' + QUOTENAME(@id) + ' AS ID, ' + QUOTENAME(@valor) + ' AS valor
        FROM ' + QUOTENAME(@tabla) + '
		where ' + QUOTENAME(@valor) + ' like ''%'+@filtro+'%'' OR '+QUOTENAME(@id)+' LIKE ''%'+@filtro+'%'''
    EXEC sp_executesql @sql
END

CREATE procedure obtenerColumnas
@nombreTabla varchar(255),
@columna varchar(255)
as
begin
SELECT COLUMN_NAME
FROM INFORMATION_SCHEMA.COLUMNS
WHERE TABLE_NAME = @nombreTabla AND (@columna IS NULL OR COLUMN_NAME LIKE @columna + '%');
end

CREATE PROCEDURE createInput
	@nombre varchar(255),
	@tipo varchar(255),
	@valor varchar(255),
	@valorAutocomplete varchar(50),
	@valorId varchar(255),
	@valorSelect varchar(255),
	@textoSelect varchar(255),
	@checked int,
	@informacionAdicional varchar(255),
	@hora time
AS
BEGIN
	insert into Inputs values (@nombre,@tipo,@valor, @valorAutocomplete, @valorId, @valorSelect, @textoSelect,@checked, @informacionAdicional, @hora)
END;

CREATE PROCEDURE actualizarInputsReportes
    @idInput INT,
    @nombreInput VARCHAR(255),
    @tipo VARCHAR(50),
    @valor TEXT,
    @valorAutocomplete VARCHAR(255),
    @valorId VARCHAR(255),
    @valoresSelect TEXT,
    @textoSelect TEXT,
    @checked INT,
    @informacionAdicional TEXT,
    @hora TIME
AS
BEGIN
    UPDATE Inputs
    SET
        nombreInput = @nombreInput,
        tipo = @tipo,
        valor = @valor,
        valorAutocomplete = @valorAutocomplete,
        valorId = @valorId,
        valoresSelect = @valoresSelect,
        textoSelect = @textoSelect,
        checked = @checked,
        informacionAdicional = @informacionAdicional,
        hora = @hora
    WHERE idInput = @idInput;
END;

CREATE PROCEDURE updateCatalogoReporte
@idReporte int,
@descripcion VARCHAR(255),
@sp_reporte VARCHAR(255),
@activo INT,
@pesado int
AS
BEGIN
    update catalogoReportes
	set descripcion = @descripcion,
	sp_reporte = @sp_reporte,
	activo = @activo,
	ReportePesado = @pesado
	where id_reporte = @idReporte
END;

CREATE PROCEDURE selectInputReportes
	@idReporte int
AS
BEGIN
	select * from ReportesInputs
	inner join Inputs
	on ReportesInputs.idInput = Inputs.idInput
	where ReportesInputs.id_reporte = @idReporte order by ReportesInputs.orden asc
END;

ALTER PROCEDURE selectPorNombreCliente
@filtroRazonSocial NVARCHAR(100) = NULL
AS
BEGIN
select ID_CLIENTE, RAZON_SOCIAL, razon_social_abreviada --reporteCliente.id_Fcliente
from FCLIENTE 
--INNER JOIN reporteCliente ON FCLIENTE.ID_CLIENTE = reporteCliente.id_Fcliente 
where @filtroRazonSocial IS NULL OR (RAZON_SOCIAL like '%'+@filtroRazonSocial+'%')  or (ID_CLIENTE like '%'+@filtroRazonSocial+'%') or (ID_CLIENTE + ' ' + RAZON_SOCIAL like '%'+@filtroRazonSocial+'%')
END

CREATE PROCEDURE selectCorreosCliente
    @id_cliente varchar(6),
	@id_reporte int
AS
BEGIN
	select id_destinatario, correo from ReporteDestinatarios where id_Fcliente = @id_cliente AND id_reporteCliente = @id_reporte
END;

CREATE PROCEDURE insertCorreos
    @id_cliente varchar(6),
    @correo varchar(50),
	@id_reporte int
AS
BEGIN
    insert into ReporteDestinatarios values( @id_cliente, @correo, @id_reporte)
END;

CREATE procedure eliminarDestinatarioCliente
	@id_FCliente varchar(6),
	@idcorreo varchar(50),
	@id_reporteCliente int
AS
BEGIN
	delete from ReporteDestinatarios
	where id_destinatario = @idcorreo AND id_Fcliente = @id_FCliente AND id_reporteCliente = @id_reporteCliente
END;

CREATE PROCEDURE seletecTablaAutoComplete
@NombreTabla NVARCHAR(100) = NULL
AS
BEGIN
    SELECT table_name
    FROM information_schema.tables
    WHERE table_type = 'BASE TABLE' AND (@NombreTabla IS NULL OR TABLE_NAME LIKE @NombreTabla + '%') order by TABLE_NAME;
END

